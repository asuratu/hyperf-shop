<?php

declare (strict_types=1);
namespace App\Shop\Model;

use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;
/**
 * @property int $id 主键
 * @property string $title SKU 名称
 * @property string $description SKU 描述
 * @property string $price SKU 价格
 * @property int $stock 库存
 * @property int $product_id 商品表主键
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class ShopProductSku extends MineModel
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
    protected $fillable = ['id', 'title', 'description', 'price', 'stock', 'product_id', 'created_at', 'updated_at', 'deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'price' => 'decimal:2', 'stock' => 'integer', 'product_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}