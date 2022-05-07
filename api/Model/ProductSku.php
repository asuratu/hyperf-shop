<?php

declare(strict_types=1);

namespace Api\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\SoftDeletes;
use Mine\ApiModel;
use Mine\Constants\StatusCode;
use Mine\Exception\BusinessException;

/**
 * @property int $id 主键
 * @property string $title SKU 名称
 * @property string $description SKU 描述
 * @property string $price SKU 价格
 * @property int $stock 库存
 * @property int $product_id 商品表主键
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class ProductSku extends ApiModel
{
    use SoftDeletes;

    public $incrementing = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shop_product_skus';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'price', 'stock', 'product_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'price' => 'decimal:2', 'stock' => 'integer', 'product_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function decreaseStock($amount): int
    {
        if ($amount < 0) {
            throw new BusinessException(StatusCode::ERR_SUB_STOCK);
        }

        return $this->where('id', $this->id)->where('stock', '>=', $amount)->decrement('stock', $amount);
    }

    public function addStock($amount)
    {
        if ($amount < 0) {
            throw new BusinessException(StatusCode::ERR_ADD_STOCK);
        }
        $this->increment('stock', $amount);
    }
}
