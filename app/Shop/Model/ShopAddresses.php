<?php

declare(strict_types=1);

namespace App\Shop\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 主键
 * @property int $user_id 用户表主键
 * @property string $province 省
 * @property string $city 市
 * @property string $district 区
 * @property string $address 具体地址
 * @property int $zip 邮编
 * @property string $contact_name 联系人姓名
 * @property string $contact_phone 联系人电话
 * @property string $last_used_at 最后使用时间
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class ShopAddresses extends MineModel
{
    use SoftDeletes;

    public $incrementing = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shop_addresses';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'province', 'city', 'district', 'address', 'zip', 'contact_name', 'contact_phone', 'last_used_at', 'created_at', 'updated_at', 'deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'user_id' => 'integer', 'zip' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
