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

namespace Mine\Constants\Laboratory;

use Hyperf\Constants\AbstractConstants;

/**
 * Class ChatRedisKey
 * 聊天系统Redis项关键
 */
class ChatRedisKey extends AbstractConstants
{
    /**
     * @Message("在线用户与Fd绑定关系")
     */
    const ONLINE_USER_FD_KEY = 'online_user_fd_list';

    /**
     * @Message("Fd与在线用户绑定关系")
     */
    const ONLINE_FD_USER_KEY = 'online_fd_user_list';

    /**
     * @Message("用户未读的聊天记录")
     */
    const GROUP_CHAT_UNREAD_MESSAGE_BY_USER = 'group_chat_unread_message_user_';

}
