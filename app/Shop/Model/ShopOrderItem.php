<?php

declare(strict_types=1);

namespace App\Shop\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;
use Mine\ApiModel;
use Mine\MineModel;

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
class ShopOrderItem extends MineModel
{
    use SoftDeletes;

    public $incrementing = false;

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
    protected $fillable = ['id', 'order_id', 'product_id', 'product_sku_id', 'amount', 'price', 'rating', 'review', 'reviewed_at', 'created_at', 'updated_at', 'deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'order_id' => 'integer', 'product_id' => 'integer', 'product_sku_id' => 'integer', 'amount' => 'integer', 'price' => 'decimal:2', 'rating' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
