<?php

namespace Api\Event;

use Api\Model\Order;

class OrderReviewed
{
    public Order $order;

    public function __construct(Order $order,)
    {
        $this->order = $order;
    }
}
