<?php

declare(strict_types=1);

namespace Api\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Events\Saving;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\SoftDeletes;
use Log;
use Mine\ApiModel;

/**
 * @property int $id 主键
 * @property string $no 订单流水号
 * @property int $user_id 下单的用户 ID
 * @property string $address JSON 格式的收货地址
 * @property string $total_amount 订单总金额
 * @property string $remark 订单备注
 * @property string $paid_at 支付时间
 * @property string $payment_method 支付方式
 * @property string $payment_no 支付平台订单号
 * @property string $refund_status 退款状态
 * @property string $refund_no 退款单号
 * @property int $closed 订单是否已关闭
 * @property int $reviewed 订单是否已评价
 * @property string $ship_status 物流状态
 * @property string $ship_data 物流数据
 * @property string $extra 其他额外的数据
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class Order extends ApiModel
{
    use SoftDeletes;

    public $incrementing = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shop_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['no', 'user_id', 'address', 'total_amount', 'remark', 'paid_at', 'payment_method', 'payment_no', 'refund_status', 'refund_no', 'closed', 'reviewed', 'ship_status', 'ship_data', 'extra'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'total_amount' => 'decimal:2',
        'closed' => 'boolean',
        'reviewed' => 'boolean',
        'address' => 'json',
        'ship_data' => 'json',
        'extra' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $dates = [
        'paid_at',
    ];

    public function saving(Saving $event)
    {
        // 如果模型的 no 字段为空
        if (!$this->no) {
            // 调用 findAvailableNo 生成订单流水号
            $this->no = static::findAvailableNo();
            // 如果生成失败，则终止创建订单
            if (!$this->no) {
                return false;
            }
        }
        $this->refund_status = $this->refund_status ?? 'pending';
        $this->ship_status = $this->ship_status ?? 'pending';
    }

    public static function findAvailableNo(): bool|string
    {
        // 订单流水号前缀
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // 随机生成 6 位的数字
            $no = $prefix . str_pad((string)mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!static::query()->where('no', $no)->exists()) {
                return $no;
            }
        }
        logger('Api Access Log')->error('find order no failed');
        return false;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

}
