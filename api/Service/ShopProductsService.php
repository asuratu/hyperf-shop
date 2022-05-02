<?php

declare(strict_types=1);

namespace Api\Service;

use Api\Mapper\ShopProductsMapper;
use Mine\Abstracts\AbstractService;

/**
 * 商品管理服务类
 */
class ShopProductsService extends AbstractService
{
    /**
     * @var ShopProductsMapper
     */
    public $mapper;

    public function __construct(ShopProductsMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
