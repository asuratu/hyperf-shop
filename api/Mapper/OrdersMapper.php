<?php

declare(strict_types=1);

namespace Api\Mapper;

use Api\Model\Order;
use Api\Model\OrderItem;
use Api\Model\ProductSku;
use Api\Model\User;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 用户订单Mapper类
 */
class OrdersMapper extends AbstractMapper
{
    /**
     * @var Order
     */
    public $model;

    public function assignModel()
    {
        $this->model = Order::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        $query->with(['items.product', 'items.productSku']);
        return $query;
    }

    /**
     * 排序处理器
     * @param Builder $query
     * @param array|null $params
     * @return Builder
     */
    public function handleOrder(Builder $query, ?array &$params = null): Builder
    {
        $query->with(['items.product', 'items.productSku']);
        return $query;
    }

    /**
     * 生成主订单
     * @param User $user
     * @param array $data
     * @return Order
     */
    public function createOrder(User $user, array $data): Order
    {
        // 创建一个订单
        $order = new $this->model;
        $order->fill($data);
        // 订单关联到当前用户
        $order->user()->associate($user);
        // 写入数据库
        $order->save();
        return $order;
    }

    /**
     * 更新主订单
     * @param Order $order
     * @param array $data
     * @return Order
     */
    public function updateOrder(Order $order, array $data): Order
    {
        $this->filterExecuteAttributes($data, true);
        $order->update($data);
        return $order;
    }


    /**
     * 生成子订单
     * @param Order $order
     * @param ProductSku $sku
     * @param int $amount
     * @return OrderItem
     */
    public function createOrderItem(Order $order, ProductSku $sku, int $amount): OrderItem
    {
//        $orderItem = $order->items()->make([
//            'amount' => $amount,
//            'price' => $sku->price,
//        ]);
//        $orderItem->product()->associate($sku->product_id);
//        $orderItem->productSku()->associate($sku);
//        $orderItem->save();

        // 创建一个 OrderItem 并直接与当前订单关联
        $orderItem = make(OrderItem::class)
            ->fill([
                'amount' => $amount,
                'price' => $sku->price,
            ]);
        $orderItem->order()->associate($order->id);
        $orderItem->product()->associate($sku->product_id);
        $orderItem->productSku()->associate($sku);
        $orderItem->save();
        return $orderItem;
    }


}
