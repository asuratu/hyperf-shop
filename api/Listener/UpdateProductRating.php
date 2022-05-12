<?php

declare(strict_types=1);

namespace Api\Listener;

use Api\Event\OrderReviewed;
use Api\Job\DelayCloseOrder;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Mine\AsyncQueue\Queue;

/**
 * @Listener
 */
#[Listener]
class UpdateProductRating implements ListenerInterface
{

    #[Inject]
    protected Queue $queue;

    /**
     * 监听事件
     * @return string[]
     */
    public function listen(): array
    {
        return [
            OrderReviewed::class
        ];
    }

    public function process(object $event)
    {
        //推送一个队列
        $this->queue->push(new DelayCloseOrder([
            'order' => $event->order,
        ]));

        // 协程处理
            
    }
}
