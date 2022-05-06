<?php

declare(strict_types=1);

namespace App\Shop\Service;

use App\Shop\Mapper\ShopUsersMapper;
use Mine\Abstracts\AbstractService;

/**
 * 用户管理服务类
 */
class ShopUsersService extends AbstractService
{
    /**
     * @var ShopUsersMapper
     */
    public $mapper;

    public function __construct(ShopUsersMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
