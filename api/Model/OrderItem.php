<?php

declare(strict_types=1);

namespace Api\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\SoftDeletes;
use Mine\ApiModel;

/**
 * @property int $id 主键
 * @property int $order_id 所属订单 ID
 * @property int $product_id 对应商品 ID
 * @property int $product_sku_id 对应商品 SKU ID
 * @property int $amount 数量
 * @property string $price 单价
 * @property int $rating 用户打分
 * @property string $review 用户评价
 * @property string $reviewed_at 评价时间
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class OrderItem extends ApiModel
{
    use SoftDeletes;

    public $incrementing = false;
    public $timestamps = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shop_order_items';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['amount', 'price', 'rating', 'review', 'reviewed_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['order_id' => 'integer', 'product_id' => 'integer', 'product_sku_id' => 'integer', 'amount' => 'integer', 'price' => 'decimal:2', 'rating' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    protected $dates = ['reviewed_at'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productSku(): BelongsTo
    {
        return $this->belongsTo(ProductSku::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

}
