<?php

declare(strict_types=1);

namespace App\Shop\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 主键
 * @property string $title 商品名称
 * @property string $description 商品详情
 * @property string $image 商品封面图片文件路径
 * @property int $on_sale 商品是否正在售卖
 * @property float $rating 商品平均评分
 * @property int $sold_count 销量
 * @property int $review_count 评价数量
 * @property string $price SKU 最低价格
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class ShopProduct extends MineModel
{
    use SoftDeletes;

    public $incrementing = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shop_products';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'image', 'on_sale', 'rating', 'sold_count', 'review_count', 'price'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'on_sale' => 'boolean', 'rating' => 'float', 'sold_count' => 'integer', 'review_count' => 'integer', 'price' => 'decimal:2', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    // 与商品SKU关联
    public function skus(): HasMany
    {
        return $this->hasMany(ShopProductSku::class);
    }
}
