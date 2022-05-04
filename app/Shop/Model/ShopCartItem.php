<?php

declare(strict_types=1);

namespace App\Shop\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 主键
 * @property int $user_id
 * @property int $product_sku_id
 * @property int $amount 商品数量
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class ShopCartItem extends MineModel
{
    use SoftDeletes;

    public $incrementing = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shop_cart_items';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'product_sku_id', 'amount'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'user_id' => 'integer', 'product_sku_id' => 'integer', 'amount' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function productSku(): BelongsTo
    {
        return $this->belongsTo(ShopProductSku::class, 'product_sku_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(ShopUser::class, 'user_id', 'id');
    }
}
