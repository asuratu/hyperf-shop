<?php

declare(strict_types=1);

namespace Api\Job;

use Api\Service\OrdersService;
use Hyperf\AsyncQueue\Job;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UpdateProductRating extends Job
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
        $service->updateRating($order);

    }
}
