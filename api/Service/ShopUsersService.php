<?php

declare(strict_types=1);

namespace Api\Service;

use Api\Mapper\ShopUsersMapper;
use Exception;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Constants\StatusCode;
use Mine\Event\ApiUserLoginAfter;
use Mine\Event\UserLoginBefore;
use Mine\Exception\NormalStatusException;
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

    public function registerByAccount($data): string
    {
        if ($this->mapper->existsByUsername($data['username'])) {
            throw new NormalStatusException(StatusCode::getMessage(StatusCode::ERR_USER_EXIST), StatusCode::ERR_USER_EXIST);
        }

        $this->evDispatcher->dispatch(new UserLoginBefore($data));
        $userId = $this->mapper->save($data);
        $userinfo = $this->mapper->read($userId)->toArray();
        $userLoginAfter = new ApiUserLoginAfter($userinfo);
        $userLoginAfter->message = t('jwt.register_success');
        try {
            $token = user('api')->getToken($userinfo);
        } catch (Exception $e) {
            console()->error($e->getMessage());
            throw new NormalStatusException(t('jwt.unknown_error'));
        }
        $userLoginAfter->token = $token;
        $this->evDispatcher->dispatch($userLoginAfter);
        return $token;
    }

    /**
     * 用户登陆
     * @param array $data
     * @return string|null
     * @throws InvalidArgumentException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function login(array $data): ?string
    {
        try {
            $this->evDispatcher->dispatch(new UserLoginBefore($data));
            $userinfo = $this->mapper->checkUserByUsername($data['username'])->toArray();
            $password = $userinfo['password'];
            unset($userinfo['password']);
            $userLoginAfter = new ApiUserLoginAfter($userinfo);
            if ($this->mapper->checkPass($data['password'], $password)) {
                if (
                    ($userinfo['status'] == SystemUser::USER_NORMAL)
                    ||
                    ($userinfo['status'] == SystemUser::USER_BAN && $userinfo['id'] == env('SUPER_ADMIN'))
                ) {
                    $userLoginAfter->message = t('jwt.login_success');
                    $token = user()->getToken($userLoginAfter->userinfo);
                    $userLoginAfter->token = $token;
                    $this->evDispatcher->dispatch($userLoginAfter);
                    return $token;
                } else {
                    $userLoginAfter->loginStatus = false;
                    $userLoginAfter->message = t('jwt.user_ban');
                    $this->evDispatcher->dispatch($userLoginAfter);
                    throw new UserBanException;
                }
            } else {
                $userLoginAfter->loginStatus = false;
                $userLoginAfter->message = t('jwt.password_error');
                $this->evDispatcher->dispatch($userLoginAfter);
                throw new NormalStatusException;
            }
        } catch (Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                throw new NormalStatusException(t('jwt.username_error'), MineCode::NO_DATA);
            }
            if ($e instanceof NormalStatusException) {
                throw new NormalStatusException(t('jwt.password_error'), MineCode::PASSWORD_ERROR);
            }
            if ($e instanceof UserBanException) {
                throw new NormalStatusException(t('jwt.user_ban'), MineCode::USER_BAN);
            }
            if ($e instanceof CaptchaException) {
                throw new NormalStatusException(t('jwt.code_error'));
            }
            console()->error($e->getMessage());
            throw new NormalStatusException(t('jwt.unknown_error'));
        }
    }

    /**
     * 用户退出
     * @throws InvalidArgumentException
     */
//    public function logout()
//    {
//        $user = user();
//        $this->evDispatcher->dispatch(new UserLogout($user->getUserInfo()));
//        $user->getJwt()->logout();
//    }

    /**
     * 获取用户信息
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
//    public function getInfo(): array
//    {
//        if (($uid = user()->getId())) {
//            return $this->getCacheInfo((int)$uid);
//        }
//        throw new MineException(t('system.unable_get_userinfo'), 500);
//    }

    /**
     * 获取缓存用户信息
     * @param int $id
     * @return array
     */
//    #[Cacheable(prefix: "loginInfo", ttl: 0, value: "userId_#{id}")]
//    protected function getCacheInfo(int $id): array
//    {
//        $user = $this->mapper->getModel()->find($id);
//        $user->addHidden('deleted_at', 'password');
//        $data['user'] = $user->toArray();
//        if (user()->isSuperAdmin()) {
//            $data['roles'] = ['superAdmin'];
//            $data['routers'] = $this->sysMenuService->mapper->getSuperAdminRouters();
//            $data['codes'] = ['*'];
//        } else {
//            $roles = $this->sysRoleService->mapper->getMenuIdsByRoleIds($user->roles()->pluck('id')->toArray());
//            $ids = $this->filterMenuIds($roles);
//            $data['roles'] = $user->roles()->pluck('code')->toArray();
//            $data['routers'] = $this->sysMenuService->mapper->getRoutersByIds($ids);
//            $data['codes'] = $this->sysMenuService->mapper->getMenuCode($ids);
//        }
//
//        return $data;
//    }


    /**
     * 新增用户
     * @param array $data
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
//    public function save(array $data): int
//    {
//        if ($this->mapper->existsByUsername($data['username'])) {
//            throw new NormalStatusException(t('system.username_exists'));
//        } else {
//            return $this->mapper->save($data);
//        }
//    }

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


}
