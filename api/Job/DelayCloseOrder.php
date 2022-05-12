<?php

declare(strict_types=1);

namespace Api\Job;

use Api\Service\OrdersService;
use Exception;
use Hyperf\AsyncQueue\Job;
use Mine\Foundation\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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
        $this->params = $params;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle()
    {
        $order = $this->params['order'];
        $service = container()->get(OrdersService::class);

        try {
            Log::codeDebug()->info('延迟队列开始');
            // 判断对应的订单是否已经被支付
            // 如果已经支付则不需要关闭订单，直接退出
            if ($order->paid_at) {
                return;
            }
            $service->closeOrder($order);
        } catch (Exception $e) {
            Log::jobLog()->error($e->getMessage());
        }
    }
}
