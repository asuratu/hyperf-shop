<?php

declare(strict_types=1);

namespace Api\Service;

use Api\Mapper\ShopProductsMapper;
use Api\Mapper\ShopUsersMapper;
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

    /**
     * 收藏商品列表
     * @param $data
     * @return array
     */
    public function favorites($data): array
    {
        return $this->userMapper->favoriteProducts($data);
    }

    /**
     * 添加商品到购物车
     * @param $data
     * @return bool
     */
    public function addCart($data): bool
    {
        $user = $this->userMapper->getUser();

        if ($cart = $this->mapper->cartItem($user, $data['sku_id'])) {
            // 如果存在则直接叠加商品数量
            // 先判库存
            if ($cart->amount + $data['amount'] > $cart->productSku->stock) {
                throw new BusinessException(StatusCode::VALIDATE_FAILED, '库存不足');
            }
            return $this->mapper->updateCartItem($cart, $data['amount']);
        } else {
            // 不存在记录, 则创建一条购物车记录
            return $this->mapper->createCartItem($user, $data['sku_id'], $data['amount']);
        }
    }

    /**
     * 购物车中移除商品
     * @param string $ids
     * @return void
     */
    public function remove(string $ids): void
    {
        $this->userMapper->removeCartItem(explode(',', $ids));
    }

    /**
     * 购物车列表
     * @param array $data
     * @return array
     */
    public function cartList(array $data): array
    {
        return $this->userMapper->cartList($data);
    }

}
