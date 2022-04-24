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

namespace Mine\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * Class StatusCode
 * 错误码枚举类
 * 自定义业务代码规范如下：
 * 默认常用的HTTP状态码
 * 接口相关，10001……
 * 用户相关，1001……
 * 业务相关，2001……
 *
 * @Constants
 */
class StatusCode extends AbstractConstants
{
    /**
     * @Message("ok")
     */
    public const SUCCESS = 200;

    /**
     * @Message("校验登陆不通过！")
     */
    public const ERR_NOT_ACCESS = 401;

    /**
     * @Message("无权限访问！")
     */
    public const ERR_NOT_PERMISSION = 403;


    /**
     * @Message("Internal Server Error!")
     */
    public const ERR_SERVER = 500;

    /**
     * @Message("系统维护中。。。!")
     */
    public const ERR_MAINTAIN = 404;


    /**
     * @Message("令牌过期！")
     */
    public const ERR_EXPIRE_TOKEN = 1002;

    /**
     * @Message("令牌无效！")
     */
    public const ERR_INVALID_TOKEN = 1003;

    /**
     * @Message("令牌不存在！")
     */
    public const ERR_NOT_EXIST_TOKEN = 1004;

    /**
     * @Message("验证码错误！")
     */
    public const ERR_CODE = 1005;

    /**
     * @Message("请登录！")
     */
    public const ERR_NOT_LOGIN = 2001;

    /**
     * @Message("用户信息错误！")
     */
    public const ERR_USER_INFO = 2002;

    /**
     * @Message("用户不存在！")
     */
    public const ERR_USER_ABSENT = 2003;

    /**
     * @Message("用户密码错误！")
     */
    public const ERR_USER_PASSWORD = 2004;

    /**
     * @Message("用户被禁用！")
     */
    public const ERR_USER_DISABLE = 2005;

    /**
     * @Message("用户名已经被使用！")
     */
    public const ERR_USER_EXIST = 2006;

    /**
     * @Message("注册失败！")
     */
    public const ERR_REGISTER_ERROR = 2007;


    /**
     * @Message("业务逻辑异常！")
     */
    public const ERR_EXCEPTION = 3001;

    /**
     * @Message("验证异常！")
     */
    public const ERR_VALIDATION = 3002;
}
