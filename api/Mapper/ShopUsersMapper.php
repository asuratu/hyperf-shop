<?php

declare(strict_types=1);

namespace Api\Mapper;

use App\Shop\Model\ShopUsers;
use Mine\Abstracts\AbstractMapper;

/**
 * 前台用户Mapper类
 */
class ShopUsersMapper extends AbstractMapper
{
    /**
     * @var ShopUsers
     */
    public $model;

    public function assignModel()
    {
        $this->model = ShopUsers::class;
    }

    /**
     * 通过用户名检查是否存在
     * @param string $username
     * @return bool
     */
    public function existsByUsername(string $username): bool
    {
        return $this->model::query()->where('username', $username)->exists();
    }

    /**
     * 检查用户密码
     * @param String $password
     * @param string $hash
     * @return bool
     */
    public function checkPass(string $password, string $hash): bool
    {
        return $this->model::passwordVerify($password, $hash);
    }

   
}
