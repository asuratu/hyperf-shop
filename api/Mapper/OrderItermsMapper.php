<?php

declare(strict_types=1);

namespace Api\Mapper;

use Api\Model\Order;
use Api\Model\OrderItem;
use Carbon\Carbon;
use Hyperf\DbConnection\Db;
use Mine\Abstracts\AbstractMapper;
use Mine\Annotation\Transaction;

/**
 * 用户子订单Mapper类
 */
class OrderItermsMapper extends AbstractMapper
{
    /**
     * @var OrderItem
     */
    public $model;

    public function assignModel()
    {
        $this->model = OrderItem::class;
    }

    #[Transaction]
    public function saveReview(Order $order, array $reviews)
    {
        // 遍历用户提交的数据
        foreach ($reviews as $review) {
            $orderItem = $order->items()->find($review['id']);
            // 保存评分和评价
            $orderItem->update([
                'rating' => $review['rating'],
                'review' => $review['review'],
                'reviewed_at' => Carbon::now(),
            ]);
        }
        // 将订单标记为已评价
        $order->update(['reviewed' => true]);
    }

    /**
     * 更新子订单中商品的平均评分
     * @param OrderItem $item
     * @return void
     */
    public function updateRatingOrderItems(OrderItem $item): void
    {
        $result = $this->model::query()
            ->where('product_id', $item->product_id)
            ->whereNotNull('reviewed_at')
            ->whereHas('order', function ($query) {
                $query->whereNotNull('paid_at');
            })
            ->first([
                DB::raw('count(*) as review_count'),
                DB::raw('avg(rating) as rating')
            ]);

        // 更新商品的评分和评价数
        $item->product->update([
            'rating' => $result->rating,
            'review_count' => $result->review_count,
        ]);
    }
}
