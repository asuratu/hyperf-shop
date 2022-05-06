<?php

declare(strict_types=1);

namespace Api\Mapper;

use Api\Model\Address;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 收货地址管理Mapper类
 */
class AddressesMapper extends AbstractMapper
{
    /**
     * @var Address
     */
    public $model;

    public function assignModel()
    {
        $this->model = Address::class;
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

        return $query;
    }
}
