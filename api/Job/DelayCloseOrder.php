<?php

declare(strict_types=1);

namespace Api\Job;

use Exception;
use Hyperf\AsyncQueue\Job;
use Hyperf\DbConnection\Db;
use Mine\Foundation\Facades\Log;

class DelayCloseOrder extends Job
{
    public $params;

    /**
     * 任务执行失败后的重试次数，即最大执行次数为 $maxAttempts+1 次
     * @var int
     */
    protected $maxAttempts = 2;

    public function __construct($params)
    {
        Log::codeDebug()->info('进入延迟队列', $params);
        $this->params = $params;
    }

    public function handle()
    {
        $order = $this->params['order'];
        try {
            Log::codeDebug()->info('延迟队列开始');
            // 判断对应的订单是否已经被支付
            // 如果已经支付则不需要关闭订单，直接退出
            if ($order->paid_at) {
                return;
            }
            // 通过事务执行 sql
            DB::transaction(function () use ($order) {
                // 将订单的 closed 字段标记为 true，即关闭订单
                $order->update(['closed' => true]);
                // 循环遍历订单中的商品 SKU，将订单中的数量加回到 SKU 的库存中去
                foreach ($order->items as $item) {
                    $item->productSku->addStock($item->amount);
                }
            });
        } catch (Exception $e) {
            Log::jobLog()->error($e->getMessage());
        }
    }
}
