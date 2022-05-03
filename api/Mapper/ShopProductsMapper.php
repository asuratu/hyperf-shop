<?php

declare(strict_types=1);

namespace Api\Mapper;

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
     * 用户收藏的商品列表
     * @param ShopUser $shopUser
     * @return array
     */
    public function favoriteProducts(ShopUser $shopUser): array
    {
        $products = $shopUser->favoriteProducts()
            ->where('on_sale', ShopProducts::ON_SALE)
            ->paginate();

        return $this->setPaginate($products);
    }
}
