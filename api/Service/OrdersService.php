<?php

declare(strict_types=1);

namespace Api\Service;

use Api\Mapper\AddressesMapper;
use Api\Mapper\OrdersMapper;
use Api\Mapper\ProductsMapper;
use Api\Mapper\UsersMapper;
use Api\Model\Order;
use Carbon\Carbon;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\Constants\StatusCode;
use Mine\Exception\BusinessException;

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

    public function __construct(OrdersMapper $mapper, UsersMapper $usersMapper, AddressesMapper $addressesMapper, ProductsMapper $productsMapper)
    {
        $this->mapper = $mapper;
        $this->usersMapper = $usersMapper;
        $this->addressesMapper = $addressesMapper;
        $this->productsMapper = $productsMapper;
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
}
