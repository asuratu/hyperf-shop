<?php

declare(strict_types=1);

namespace Api\Service;

use Api\Mapper\ShopUsersMapper;
use App\Shop\Model\ShopUser;
use Exception;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\Di\Annotation\Inject;
use JetBrains\PhpStorm\ArrayShape;
use Mine\Abstracts\AbstractService;
use Mine\Constants\StatusCode;
use Mine\Event\ApiUserLoginAfter;
use Mine\Event\UserLoginBefore;
use Mine\Event\UserLogout;
use Mine\Exception\BusinessException;
use Mine\Exception\MineException;
use Mine\Exception\NormalStatusException;
use Mine\Exception\UserBanException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * 用户管理服务类
 */
class ShopUsersService extends AbstractService
{
    /**
     * @var ShopUsersMapper
     */
    public $mapper;

    /**
     * @var EventDispatcherInterface
     */
    #[InJect]
    protected EventDispatcherInterface $evDispatcher;

    public function __construct(ShopUsersMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 账号密码注册
     * @param array $data
     * @return array
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundExceptionInterface
     */
    #[ArrayShape(['userinfo' => "\Hyperf\Database\Model\Model", 'token' => "string"])]
    public function registerByAccount(array $data): array
    {
        $userinfo = $this->saveUser($data);
        // 用户信息转数组
        $userinfoArr = $userinfo->toArray();
        // 登录之后的事件
        $userLoginAfter = new ApiUserLoginAfter($userinfoArr);
        $userLoginAfter->message = t('jwt.register_success');
        // 生成token
        try {
            $token = user('api')->getToken($userinfoArr);
        } catch (Exception $e) {
            console()->error($e->getMessage());
            throw new BusinessException(StatusCode::ERR_SERVER);
        }
        $userLoginAfter->token = $token;
        // 调度登录之后的事件
        $this->evDispatcher->dispatch($userLoginAfter);
        return [
            'userinfo' => $userinfo,
            'token' => $token,
        ];
    }

    /**
     * 新增用户
     * @param array $data
     * @return Model
     * @throws NormalStatusException
     */
    public function saveUser(array $data): Model
    {
        if ($this->mapper->existsByUsername($data['username'])) {
            throw new BusinessException(StatusCode::ERR_USER_EXIST);
        }
        // 登录之前的事件
        $this->evDispatcher->dispatch(new UserLoginBefore($data));
        // 新增用户
        return $this->mapper->create($data);
    }

    /**
     * 用户登陆
     * @param array $data
     * @return array
     * @throws InvalidArgumentException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[ArrayShape(['userinfo' => "array", 'token' => "string"])]
    public function login(array $data): array
    {
        try {
            $this->evDispatcher->dispatch(new UserLoginBefore($data));
            $userinfo = $this->mapper->checkUserByUsername($data['username']);
            $userinfoArr = $userinfo->toArray();
            $password = $userinfoArr['password'];
            unset($userinfoArr['password']);
            $userLoginAfter = new ApiUserLoginAfter($userinfoArr);
            if ($this->mapper->checkPass($data['password'], $password)) {
                if ($userinfo['status'] == ShopUser::USER_BAN) {
                    $userLoginAfter->loginStatus = false;
                    $userLoginAfter->message = t('jwt.user_ban');
                    $this->evDispatcher->dispatch($userLoginAfter);
                    throw new UserBanException;
                }

                $userLoginAfter->message = t('jwt.login_success');
                $token = user()->getToken($userLoginAfter->userinfo);
                $userLoginAfter->token = $token;
                $this->evDispatcher->dispatch($userLoginAfter);
                return [
                    'userinfo' => $userinfo,
                    'token' => $token,
                ];
            } else {
                $userLoginAfter->loginStatus = false;
                $userLoginAfter->message = t('jwt.password_error');
                $this->evDispatcher->dispatch($userLoginAfter);
                throw new BusinessException;
            }
        } catch (Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                throw new BusinessException(StatusCode::ERR_USER_ABSENT);
            }
            if ($e instanceof BusinessException) {
                throw new BusinessException(StatusCode::ERR_USER_PASSWORD);
            }
            if ($e instanceof UserBanException) {
                throw new BusinessException(StatusCode::ERR_USER_DISABLE);
            }
            console()->error($e->getMessage());
            throw new BusinessException(StatusCode::ERR_SERVER);
        }
    }

    /**
     * 用户退出
     * @throws InvalidArgumentException
     */
    public function logout()
    {
        $user = user('api');
        $this->evDispatcher->dispatch(new UserLogout($user->getUserInfo()));
        $user->getJwt()->logout();
    }

    /**
     * 获取用户信息
     * @return ShopUser
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getInfo(): ShopUser
    {
        if (($uid = user('api')->getId())) {
            return $this->getCacheInfo((int)$uid);
        }
        throw new MineException(t('system.unable_get_userinfo'), 500);
    }

    /**
     * 获取缓存用户信息
     * @param int $id
     * @return ShopUser
     */
    #[Cacheable(prefix: "loginInfo", ttl: 0, value: "userId_#{id}")]
    protected function getCacheInfo(int $id): ShopUser
    {
        $user = $this->mapper->getModel()->findOrFail($id);
        if (!$user instanceof ShopUser) {
            throw new ModelNotFoundException();
        }
        $user->addHidden('deleted_at', 'password');
        return $user;
    }

    /**
     * 更新用户信息
     * @param int $id
     * @param array $data
     * @return bool
     */
//    #[CacheEvict(prefix: "loginInfo", value: "userId_#{id}")]
//    public function update(int $id, array $data): bool
//    {
//        if (isset($data['username'])) {
//            unset($data['username']);
//        }
//        if (isset($data['password'])) {
//            unset($data['password']);
//        }
//        return $this->mapper->update($id, $this->handleData($data));
//    }


    /**
     * 检查用户是否收藏过某商品
     * @param ShopUser $user
     * @param int $productId
     * @return bool
     */
    public function existsFavoriteProduct(ShopUser $user, int $productId): bool
    {
        return $user->favoriteProducts()
            ->where('shop_products.id', $productId)
            ->exists();
    }
}
