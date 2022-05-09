<?php
declare(strict_types=1);

namespace Mine\Foundation\Facades;

use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;
use Psr\Log\LoggerInterface;

/**
 * 日志工具类
 * Class Log
 */
class Log
{

    /**
     * debug调试日志
     * @return LoggerInterface
     */
    public static function codeDebug(): LoggerInterface
    {
        return self::channel('code_debug', config('app_env', 'app'));
    }

    /**
     * 日志通道
     * @param string $group
     * @param string $name
     * @return LoggerInterface
     */
    public static function channel(string $group = 'default', string $name = 'app'): LoggerInterface
    {
        return ApplicationContext::getContainer()->get(LoggerFactory::class)->get($name, $group);
    }

    /**
     * 接口请求日志
     * @return LoggerInterface
     */
    public static function requestLog(): LoggerInterface
    {
        return self::channel('request', config('app_env', 'app'));
    }

    /**
     * 接口返回日志
     * @return LoggerInterface
     */
    public static function responseLog(): LoggerInterface
    {
        return self::channel('response', config('app_env', 'app'));
    }

    /**
     * sql记录日志
     * @return LoggerInterface
     */
    public static function sqlLog(): LoggerInterface
    {
        return self::channel('sql', config('app_env', 'app'));
    }

    /**
     * 队列错误日志
     * @return LoggerInterface
     */
    public static function jobLog(): LoggerInterface
    {
        return self::channel('job', config('app_env', 'app'));
    }

    /**
     * 定时任务错误日志
     * @return LoggerInterface
     */
    public static function crontabLog(): LoggerInterface
    {
        return self::channel('crontab', config('app_env', 'app'));
    }
}
