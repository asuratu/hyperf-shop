<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace Api\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * Class OrderStatus
 * 订单状态枚举类
 *
 * @Constants
 */
class OrderStatus extends AbstractConstants
{
    /**
     * @Message("未发货")
     */
    public const SHIP_STATUS_PENDING = 'pending';

    /**
     * @Message("已发货")
     */
    public const SHIP_STATUS_DELIVERED = 'delivered';

    /**
     * @Message("已收货")
     */
    public const SHIP_STATUS_RECEIVED = 'received';
}
