<?php

declare(strict_types=1);

namespace Api\Service;

use Api\Constants\OrderStatus;
use Api\Event\OrderReviewed;
use Api\Mapper\AddressesMapper;
use Api\Mapper\OrderItermsMapper;
use Api\Mapper\OrdersMapper;
use Api\Mapper\ProductsMapper;
use Api\Mapper\UsersMapper;
use Api\Model\Order;
use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Exception\NotFoundException;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\ApiModel;
use Mine\Constants\StatusCode;
use Mine\Exception\BusinessException;
use Mine\MineModel;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * 订单服务类
 */
class OrdersService extends AbstractService
{
    /**
     * @var OrdersMapper
     */
    public $mapper;
    /**
     * @var OrderItermsMapper
     */
    public OrderItermsMapper $orderItermsMapper;
    /**
     * @var UsersMapper
     */
    public UsersMapper $usersMapper;
    /**
     * @var AddressesMapper
     */
    public AddressesMapper $addressesMapper;
    /**
     * @var ProductsMapper
     */
    public ProductsMapper $productsMapper;

    /**
     * 事件调度器
     * @var EventDispatcherInterface
     */
    #[Inject]
    protected EventDispatcherInterface $evDispatcher;

    public function __construct(OrdersMapper $mapper, UsersMapper $usersMapper, AddressesMapper $addressesMapper, ProductsMapper $productsMapper, OrderItermsMapper $orderItermsMapper)
    {
        $this->mapper = $mapper;
        $this->usersMapper = $usersMapper;
        $this->addressesMapper = $addressesMapper;
        $this->productsMapper = $productsMapper;
        $this->orderItermsMapper = $orderItermsMapper;
    }

    #[Transaction]
    public function createOrder($data): Order
    {
        $user = $this->usersMapper->getUser();

        $address = $this->addressesMapper->read($data['address_id']);
        // 更新此地址的最后使用时间
        $address->update(['last_used_at' => Carbon::now()]);

        $orderData = [
            'address' => [ // 将地址信息放入订单中
                'address' => $address->full_address,
                'zip' => $address->zip,
                'contact_name' => $address->contact_name,
                'contact_phone' => $address->contact_phone,
            ],
            'remark' => $data['remark'] ?? '',
            'total_amount' => 0,
        ];

        $order = $this->mapper->createOrder($user, $orderData);

        // 订单总金额
        $totalAmount = '0';
        $items = $data['items'];
        $skuIds = collect($items)->pluck('sku_id')->toArray();
        $productSkus = $this->productsMapper->productSkus($skuIds, ['id', 'product_id', 'price', 'stock'])->keyBy('id');

        // 遍历用户提交的 SKU
        foreach ($items as $item) {
            $sku = $productSkus[$item['sku_id']];
            $this->mapper->createOrderItem($order, $sku, $item['amount']);
            $totalAmount = bcadd($totalAmount, bcmul($sku->price, (string)$item['amount']));
            if ($sku->decreaseStock($item['amount']) <= 0) {
                throw new BusinessException(StatusCode::ERR_STOCK_LESS);
            }
        }

        // 更新订单总金额
        $this->mapper->updateOrder($order, ['total_amount' => $totalAmount]);

        // 将下单的商品从购物车中移除
        $this->usersMapper->removeCartItem($user, $skuIds);

        return $order;
    }

    /**
     * 确认收货
     * @param int $id
     * @return bool
     * @throws NotFoundException
     */
    public function received(int $id): bool
    {
        $order = $this->mapper->myRead($id, user('api')->getId());

        // 判断订单的发货状态是否为已发货
        if ($order->ship_status !== OrderStatus::SHIP_STATUS_DELIVERED) {
            throw new BusinessException(StatusCode::ERR_ORDER_SHIP_STATUS);
        }

        return $this->mapper->update($id, [
            'ship_status' => OrderStatus::SHIP_STATUS_RECEIVED
        ]);
    }

    /**
     * 读取用户相关的一条数据
     * @param int $id
     * @return MineModel|ApiModel
     * @throws NotFoundException
     */
    public function myRead(int $id): MineModel|ApiModel
    {
        $order = $this->mapper->myRead($id, user('api')->getId());
        return $order->load(['items.product', 'items.productSku']);
    }

    /**
     * 评价商品
     * @param int $id
     * @param array $data
     * @throws NotFoundException
     */
    public function review(int $id, array $data)
    {
        // 订单信息
        $order = $this->mapper->detail($id, user('api')->getId());

        $reviews = $data['reviews'];

        // 判断是否已经支付
        if (!$order->paid_at) {
            throw new BusinessException(StatusCode::ERR_UNPAID);
        }

        // 判断是否已经评价
        if ($order->reviewed) {
            throw new BusinessException(StatusCode::ERR_EVALUATED);
        }

        // 检查参数
        $orderIds = collect($reviews)->pluck('id');

        // 检查是否是子订单id
        $count = $this->orderItermsMapper->count(function ($query) use ($id, $orderIds) {
            return $query->where('order_id', $id)
                ->whereIn('id', $orderIds);
        });

        if ($count != count($reviews)) {
            throw new BusinessException(StatusCode::VALIDATE_FAILED);
        }

        $this->orderItermsMapper->saveReview($order, $reviews);

        $orderReviewed = new OrderReviewed($order);
        $this->evDispatcher->dispatch($orderReviewed);
    }

    /**
     * 评价详情
     * @param int $id
     * @param array $data
     * @throws NotFoundException
     */
    public function reviewDetail(int $id, array $data)
    {
        // 订单信息
        $order = $this->mapper->detail($id, user('api')->getId());

        // 判断是否已经支付
        if (!$order->paid_at) {
            throw new BusinessException(StatusCode::ERR_UNPAID);
        }

        // 判断是否已经评价
        if ($order->reviewed) {
            throw new BusinessException(StatusCode::ERR_EVALUATED);
        }
    }

    /**
     * 关闭订单
     * @param Order $order
     */
    #[Transaction]
    public function closeOrder(Order $order)
    {
        // 将订单的 closed 字段标记为 true，即关闭订单
        $order->update(['closed' => true]);
        // 循环遍历订单中的商品 SKU，将订单中的数量加回到 SKU 的库存中去
        foreach ($order->items as $item) {
            $item->productSku->addStock($item->amount);
        }
    }

    /**
     * 更新评分
     * @param Order $order
     */
    #[Transaction]
    public function updateRating(Order $order)
    {
        // 通过 with 方法提前加载数据，避免 N + 1 性能问题
        $items = $order->items()->with(['product'])->get();

        foreach ($items as $item) {
            $this->orderItermsMapper->updateRatingOrderItems($item);
        }
    }

}
