<?php

declare(strict_types=1);

namespace App\Shop\Mapper;

use App\Shop\Model\ShopUser;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 用户管理Mapper类
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
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {

        // 密码
        if (isset($params['password']) && $params['password'] !== '') {
            $query->where('password', '=', $params['password']);
        }

        // 手机
        if (isset($params['phone']) && $params['phone'] !== '') {
            $query->where('phone', '=', $params['phone']);
        }

        // 用户邮箱
        if (isset($params['email']) && $params['email'] !== '') {
            $query->where('email', '=', $params['email']);
        }

        // 用户头像
        if (isset($params['avatar']) && $params['avatar'] !== '') {
            $query->where('avatar', '=', $params['avatar']);
        }

        // 状态 (0正常 1停用)
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', '=', $params['status']);
        }

        // 最后登陆IP
        if (isset($params['login_ip']) && $params['login_ip'] !== '') {
            $query->where('login_ip', '=', $params['login_ip']);
        }

        // 最后登陆时间
        if (isset($params['login_time']) && $params['login_time'] !== '') {
            $query->where('login_time', '=', $params['login_time']);
        }

        return $query;
    }
}
