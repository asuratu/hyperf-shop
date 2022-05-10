<?php

declare(strict_types=1);

namespace Api\Controller\V1;

use Api\Job\DelayCloseOrder;
use Api\Request\Product\OrderRequest;
use Api\Service\OrdersService;
use Dotenv\Exception\ValidationException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\AsyncQueue\Queue;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 订单控制器
 * Class OrdersController
 */
#[Controller(prefix: "api/v1/orders"), Auth('api')]
class OrdersController extends MineController
{
    #[Inject]
    protected OrdersService $service;

    #[Inject]
    protected Queue $queue;

    /**
     * 新增
     * @param OrderRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("save")]
    public function save(OrderRequest $request): ResponseInterface
    {
        $order = $this->service->createOrder($request->all());

        //推送一个队列
        $this->queue->push(new DelayCloseOrder([
            'order' => $order,
            'uuid' => time(),
        ]), config('app.order.ttl'));
        return $this->success();
    }

    /**
     * 订单列表
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws ValidationException
     */
    #[GetMapping("index"), Auth('api')]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getMyPageList(user('api')->getId(), $this->request->all(), false));
    }

    /**
     * 订单详情
     * @param int $id
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("read/{id}"), Auth('api')]
    public function read(int $id): ResponseInterface
    {
        $order = $this->service->myRead($id);
        $order->load(['items.product', 'items.productSku']);
        return $this->success($order);
    }

    /**
     * 确认收货
     * @param int $id
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("received/{id}"), Auth('api')]
    public function received(int $id): ResponseInterface
    {
        // 更新发货状态为已收到
        return $this->service->received($id) ? $this->success() : $this->error();
    }
}
