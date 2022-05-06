<?php

declare(strict_types=1);

namespace Api\Mapper;

use Api\Model\CartItem;
use Api\Model\Product;
use Api\Model\User;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 商品管理Mapper类
 */
class ProductsMapper extends AbstractMapper
{
    /**
     * @var Product
     */
    public $model;

    public function assignModel()
    {
        $this->model = Product::class;
    }

    /**
     * 搜索处理器
     * @param Builder $builder
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $builder, array $params): Builder
    {
        // 只查询在售的商品
        $builder->where('on_sale', Product::ON_SALE);

        // 关键词
        if (isset($params['search']) && $params['search'] !== '') {
            $like = '%' . $params['search'] . '%';
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
        }

        return $builder;
    }

    /**
     * 获取用户的购物车记录
     * @param User $user
     * @param int $skuId
     * @return CartItem|null
     */
    public function cartItem(User $user, int $skuId): CartItem|null
    {
        $cart = $user->cartItems()->where('product_sku_id', $skuId)->first();
        if (!$cart instanceof CartItem) {
            return null;
        }
        return $cart;
    }

    /**
     * 创建一条购物车记录
     * @param User $user
     * @param int $skuId
     * @param int $amount
     * @return bool
     */
    public function createCartItem(User $user, int $skuId, int $amount): bool
    {
        $cart = new CartItem(['amount' => $amount]);
        $cart->user()->associate($user);
        $cart->productSku()->associate($skuId);
        return $cart->save();
    }

    /**
     * 更新购物车记录
     * @param $cart
     * @param int $amount
     * @return bool
     */
    public function updateCartItem($cart, int $amount): bool
    {
        return $cart->update([
            'amount' => $cart->amount + $amount,
        ]);
    }
}
