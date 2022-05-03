<?php

declare(strict_types=1);

namespace Api\Mapper;

use App\Shop\Model\ShopUser;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\ModelNotFoundException;
use Mine\Abstracts\AbstractMapper;

/**
 * 前台用户Mapper类
 */
class ShopUsersMapper extends AbstractMapper
{
    /**
     * @var ShopUser
     */
    public $model;

    public function assignModel()
    {
        $this->model = ShopUser::class;
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

    /**
     * 通过用户名检查用户
     * @param string $username
     * @return Builder|Model
     * @throws ModelNotFoundException
     */
    public function checkUserByUsername(string $username): Model|Builder
    {
        return $this->model::query()->where('username', $username)->firstOrFail();
    }

    /**
     * @Title: 获取当前用户实例
     * @return ShopUser
     */
    public function getUser(): ShopUser
    {
        $user = $this->model::findOrFail((int)user('api')->getId());

        if (!$user instanceof ShopUser) {
            throw new ModelNotFoundException();
        }

        return $user;
    }


}
