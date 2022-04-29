<?php

declare(strict_types=1);

namespace Api\Service;

use App\Shop\Mapper\ShopAddressesMapper;
use Mine\Abstracts\AbstractService;

/**
 * 收货地址管理服务类
 */
class ShopAddressesService extends AbstractService
{
    /**
     * @var ShopAddressesMapper
     */
    public $mapper;

    public function __construct(ShopAddressesMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
