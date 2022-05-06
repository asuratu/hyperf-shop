<?php

declare(strict_types=1);

namespace Api\Service;

use Api\Mapper\AddressesMapper;
use Mine\Abstracts\AbstractService;

/**
 * 收货地址管理服务类
 */
class AddressesService extends AbstractService
{
    /**
     * @var AddressesMapper
     */
    public $mapper;

    public function __construct(AddressesMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
