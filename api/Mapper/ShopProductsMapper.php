<?php

declare(strict_types=1);

namespace Api\Mapper;

use App\Shop\Model\ShopCartItem;
use App\Shop\Model\ShopProducts;
use App\Shop\Model\ShopUser;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 商品管理Mapper类
 */
class ShopProductsMapper extends AbstractMapper
{
    /**
     * @var ShopProducts
     */
    public $model;

    public function assignModel()
    {
        $this->model = ShopProducts::class;
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
        $builder->where('on_sale', ShopProducts::ON_SALE);

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
     * @param ShopUser $shopUser
     * @param int $skuId
     * @return ShopCartItem|null
     */
    public function cartItem(ShopUser $shopUser, int $skuId): ShopCartItem|null
    {
        $cart = $shopUser->cartItems()->where('product_sku_id', $skuId)->first();
        if (!$cart instanceof ShopCartItem) {
            return null;
        }
        return $cart;
    }

    /**
     * 创建一条购物车记录
     * @param ShopUser $shopUser
     * @param int $skuId
     * @param int $amount
     * @return bool
     */
    public function createCartItem(ShopUser $shopUser, int $skuId, int $amount): bool
    {
        $cart = new ShopCartItem(['amount' => $amount]);
        $cart->user()->associate($shopUser);
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
