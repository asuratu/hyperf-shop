<?php

declare(strict_types=1);

namespace Api\Mapper;

use App\Shop\Model\ShopAddresses;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 收货地址管理Mapper类
 */
class ShopAddressesMapper extends AbstractMapper
{
    /**
     * @var ShopAddresses
     */
    public $model;

    public function assignModel()
    {
        $this->model = ShopAddresses::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        // 省
        if (isset($params['province']) && $params['province'] !== '') {
            $query->where('province', '=', $params['province']);
        }

        // 市
        if (isset($params['city']) && $params['city'] !== '') {
            $query->where('city', '=', $params['city']);
        }

        // 区
        if (isset($params['district']) && $params['district'] !== '') {
            $query->where('district', '=', $params['district']);
        }

        // 具体地址
        if (isset($params['address']) && $params['address'] !== '') {
            $query->where('address', '=', $params['address']);
        }

        // 邮编
        if (isset($params['zip']) && $params['zip'] !== '') {
            $query->where('zip', '=', $params['zip']);
        }

        // 联系人姓名
        if (isset($params['contact_name']) && $params['contact_name'] !== '') {
            $query->where('contact_name', '=', $params['contact_name']);
        }

        // 联系人电话
        if (isset($params['contact_phone']) && $params['contact_phone'] !== '') {
            $query->where('contact_phone', '=', $params['contact_phone']);
        }

        // 最后使用时间
        if (isset($params['last_used_at']) && $params['last_used_at'] !== '') {
            $query->where('last_used_at', '=', $params['last_used_at']);
        }

        return $query;
    }
}
