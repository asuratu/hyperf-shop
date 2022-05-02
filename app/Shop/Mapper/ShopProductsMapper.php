<?php

declare(strict_types = 1);
namespace App\Shop\Mapper;

use App\Shop\Model\ShopProducts;
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
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        
        // 商品名称
        if (isset($params['title']) && $params['title'] !== '') {
            $query->where('title', '=', $params['title']);
        }

        // 商品详情
        if (isset($params['description']) && $params['description'] !== '') {
            $query->where('description', '=', $params['description']);
        }

        // 商品封面图片文件路径
        if (isset($params['image']) && $params['image'] !== '') {
            $query->where('image', '=', $params['image']);
        }

        // 商品是否正在售卖
        if (isset($params['on_sale']) && $params['on_sale'] !== '') {
            $query->where('on_sale', '=', $params['on_sale']);
        }

        // 商品平均评分
        if (isset($params['rating']) && $params['rating'] !== '') {
            $query->where('rating', '=', $params['rating']);
        }

        // 销量
        if (isset($params['sold_count']) && $params['sold_count'] !== '') {
            $query->where('sold_count', '=', $params['sold_count']);
        }

        // 评价数量
        if (isset($params['review_count']) && $params['review_count'] !== '') {
            $query->where('review_count', '=', $params['review_count']);
        }

        // SKU 最低价格
        if (isset($params['price']) && $params['price'] !== '') {
            $query->where('price', '=', $params['price']);
        }

        return $query;
    }
}