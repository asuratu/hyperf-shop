<?php

declare(strict_types=1);

namespace Api\Service;

use Api\Mapper\ShopProductsMapper;
use Api\Mapper\ShopUsersMapper;
use App\Shop\Model\ShopUser;
use Hyperf\Database\Model\ModelNotFoundException;
use Mine\Abstracts\AbstractService;
use Mine\Constants\StatusCode;
use Mine\Exception\BusinessException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 商品管理服务类
 */
class ShopProductsService extends AbstractService
{
    /**
     * @var ShopProductsMapper
     */
    public $mapper;

    /**
     * @var ShopUsersMapper
     */
    protected ShopUsersMapper $userMapper;

    /**
     * @var ShopUsersService
     */
    protected ShopUsersService $usersService;

    /**
     * ShopProductsService constructor.
     * @param ShopProductsMapper $mapper
     * @param ShopUsersMapper $userMapper
     * @param ShopUsersService $usersService
     */
    public function __construct(ShopProductsMapper $mapper, ShopUsersMapper $userMapper, ShopUsersService $usersService)
    {
        $this->mapper = $mapper;
        $this->userMapper = $userMapper;
        $this->usersService = $usersService;
    }

    /**
     * 收藏商品
     * @param $id
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function favor($id): void
    {
        $product = $this->mapper->read($id);
        $user = $this->userMapper->getUser();

        if ($this->usersService->existsFavoriteProduct($user, $id)) {
            throw new BusinessException(StatusCode::ERR_REPEAT);
        }

        $user->favoriteProducts()->attach($product, ['id' => snowflake_id()]);
    }

    /**
     * 取消收藏商品
     * @param $id
     * @return void
     */
    public function disfavor($id): void
    {
        $product = $this->mapper->read($id);
        $user = $this->userMapper->getUser();

        if (!$this->usersService->existsFavoriteProduct($user, $id)) {
            throw new BusinessException(StatusCode::ERR_REPEAT);
        }

        $user->favoriteProducts()->detach($product);
    }
}
