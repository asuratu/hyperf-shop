<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateShopOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shop_order_items', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('订单子表');
            $table->addColumn('bigInteger', 'id', ['unsigned' => true, 'comment' => '主键']);
            $table->unsignedBigInteger('order_id')->comment('所属订单 ID');
            $table->foreign('order_id')->references('id')->on('shop_orders')->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->comment('对应商品 ID');
            $table->foreign('product_id')->references('id')->on('shop_products')->onDelete('cascade');
            $table->unsignedBigInteger('product_sku_id')->comment('对应商品 SKU ID');
            $table->foreign('product_sku_id')->references('id')->on('shop_product_skus')->onDelete('cascade');
            $table->unsignedInteger('amount')->comment('数量');
            $table->decimal('price')->default(0)->comment('单价');
            $table->unsignedInteger('rating')->nullable()->comment('用户打分');
            $table->text('review')->nullable()->comment('用户评价');
            $table->dateTime('reviewed_at')->nullable()->comment('评价时间');
            $table->addColumn('timestamp', 'created_at', ['precision' => 0, 'comment' => '创建时间'])->nullable();
            $table->addColumn('timestamp', 'updated_at', ['precision' => 0, 'comment' => '更新时间'])->nullable();
            $table->addColumn('timestamp', 'deleted_at', ['precision' => 0, 'comment' => '删除时间'])->nullable();
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_order_items');
    }
}
