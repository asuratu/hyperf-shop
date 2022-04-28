<?php

declare(strict_types=1);

namespace App\Shop\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 主键
 * @property string $username 用户名
 * @property string $phone 手机
 * @property string $email 用户邮箱
 * @property string $avatar 用户头像
 * @property string $status 状态 (0正常 1停用)
 * @property string $login_ip 最后登陆IP
 * @property string $login_time 最后登陆时间
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property string $remark 备注
 * @property-write mixed $password 密码
 */
class ShopUser extends MineModel
{
    use SoftDeletes;

    public const USER_NORMAL = 0;
    public const USER_BAN = 1;
    public $incrementing = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shop_users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'username', 'password', 'phone', 'email', 'avatar', 'status', 'login_ip', 'login_time', 'created_at', 'updated_at', 'deleted_at', 'remark'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    /**
     * 验证密码
     * @param $password
     * @param $hash
     * @return bool
     */
    public static function passwordVerify($password, $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * 密码加密
     * @param $value
     * @return void
     */
    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = password_hash($value, PASSWORD_DEFAULT);
    }
}
