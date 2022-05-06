<?php

declare(strict_types=1);

namespace Api\Mapper;

use Api\Model\Product;
use Api\Model\User;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\ModelNotFoundException;
use Mine\Abstracts\AbstractMapper;

/**
 * 前台用户Mapper类
 */
class UsersMapper extends AbstractMapper
{
    /**
     * @var User
     */
    public $model;

    public function assignModel()
    {
        $this->model = User::class;
    }

    /**
     * 通过用户名检查是否存在
     * @param string $username
     * @return bool
     */
    public function existsByUsername(string $username): bool
    {
        return $this->model::query()->where('username', $username)->exists();
    }

    /**
     * 检查用户密码
     * @param String $password
     * @param string $hash
     * @return bool
     */
    public function checkPass(string $password, string $hash): bool
    {
        return $this->model::passwordVerify($password, $hash);
    }

    /**
     * 通过用户名检查用户
     * @param string $username
     * @return Builder|Model
     * @throws ModelNotFoundException
     */
    public function checkUserByUsername(string $username): Model|Builder
    {
        return $this->model::query()->where('username', $username)->firstOrFail();
    }

    /**
     * 用户的购物车列表
     * @param $data
     * @return array
     */
    public function cartList($data): array
    {
        $user = $this->getUser();

        $cartItems = $user->cartItems()
            // 只查询当前 on_sale 为 1 的商品
            ->whereHas('productSku', function ($query) {
                $query->whereHas('product', function ($query) {
                    $query->where('on_sale', Product::ON_SALE);
                });
            })
            ->with('productSku')
            ->paginate(
                (int)$data['pageSize'] ?? $this->model::PAGE_SIZE,
                ['*'],
                'page',
                (int)$data['page'] ?? 1
            );

        return $this->setPaginate($cartItems);
    }

    /**
     * 获取当前用户实例
     * @return User
     */
    public function getUser(): User
    {
        $user = $this->model::findOrFail((int)user('api')->getId());

        if (!$user instanceof User) {
            throw new ModelNotFoundException();
        }

        return $user;
    }

    /**
     * 用户收藏的商品列表
     * @param array $skuIdArr
     * @return void
     */
    public function removeCartItem(array $skuIdArr): void
    {
        $user = $this->getUser();
        $user->cartItems()
            ->whereIn('product_sku_id', $skuIdArr)
            ->delete();
    }

    /**
     * 用户收藏的商品列表
     * @param $data
     * @return array
     */
    public function favoriteProducts($data): array
    {
        $user = $this->getUser();

        $products = $user->favoriteProducts()
            ->where('on_sale', Product::ON_SALE)
            ->with('skus')
            ->paginate(
                (int)$data['pageSize'] ?? $this->model::PAGE_SIZE,
                ['*'],
                'page',
                (int)$data['page'] ?? 1
            );

        return $this->setPaginate($products);
    }
}
