<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

use App\System\Service\SystemQueueLogService;
use App\System\Vo\AmqpQueueVo;
use App\System\Vo\QueueMessageVo;
use Hyperf\Context\Context;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;
use Mine\Helper\AppVerify;
use Mine\Helper\Id;
use Mine\Helper\LoginUser;
use Mine\MineCollection;
use Mine\MineRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

if (!function_exists('container')) {

    /**
     * 获取容器实例
     * @return ContainerInterface
     */
    function container(): ContainerInterface
    {
        return ApplicationContext::getContainer();
    }
}

if (!function_exists('redis')) {

    /**
     * 获取Redis实例
     * @return \Hyperf\Redis\Redis
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function redis(): \Hyperf\Redis\Redis
    {
        return container()->get(\Hyperf\Redis\Redis::class);
    }
}

if (!function_exists('console')) {

    /**
     * 获取控制台输出实例
     * @return StdoutLoggerInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function console(): StdoutLoggerInterface
    {
        return container()->get(StdoutLoggerInterface::class);
    }
}

if (!function_exists('logger')) {

    /**
     * 获取日志实例
     * @param string $name
     * @return LoggerInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function logger(string $name = 'Log'): LoggerInterface
    {
        return container()->get(LoggerFactory::class)->get($name);
    }
}

if (!function_exists('user')) {
    /**
     * 获取当前登录用户实例
     * @param string $scene
     * @return LoginUser
     */
    function user(string $scene = 'default'): LoginUser
    {
        return new LoginUser($scene);
    }
}

if (!function_exists('format_size')) {
    /**
     * 格式化大小
     * @param int $size
     * @return string
     */
    function format_size(int $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $index = 0;
        for ($i = 0; $size >= 1024 && $i < 5; $i++) {
            $size /= 1024;
            $index = $i;
        }
        return round($size, 2) . $units[$index];
    }
}

if (!function_exists('t')) {
    /**
     * 多语言函数
     * @param string $key
     * @param array $replace
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function t(string $key, array $replace = []): string
    {
        $acceptLanguage = container()->get(MineRequest::class)->getHeaderLine('accept-language');
        $language = !empty($acceptLanguage) ? explode(',', $acceptLanguage)[0] : 'zh_CN';
        return __($key, $replace, $language);
    }
}

if (!function_exists('mine_collect')) {
    /**
     * 创建一个Mine的集合类
     * @param null|mixed $value
     * @return MineCollection
     */
    function mine_collect($value = null): MineCollection
    {
        return new MineCollection($value);
    }
}

if (!function_exists('context_set')) {
    /**
     * 设置上下文数据
     * @param string $key
     * @param $data
     * @return bool
     */
    function context_set(string $key, $data): bool
    {
        return (bool)Context::set($key, $data);
    }
}

if (!function_exists('context_get')) {
    /**
     * 获取上下文数据
     * @param string $key
     * @return mixed
     */
    function context_get(string $key)
    {
        return Context::get($key);
    }
}

if (!function_exists('app_verify')) {
    /**
     * 获取APP应用请求实例
     * @param string $scene
     * @return AppVerify
     */
    function app_verify(string $scene = 'api'): AppVerify
    {
        return new AppVerify($scene);
    }
}

if (!function_exists('snowflake_id')) {
    /**
     * 生成雪花ID
     * @return String
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function snowflake_id(): string
    {
        return container()->get(Id::class)->getId();
    }
}

if (!function_exists('event')) {
    /**
     * 事件调度快捷方法
     * @param object $dispatch
     * @return object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function event(object $dispatch): object
    {
        return container()->get(EventDispatcherInterface::class)->dispatch($dispatch);
    }
}

if (!function_exists('push_queue_message')) {
    /**
     * 推送消息到队列
     * @param QueueMessageVo $message
     * @param array $receiveUsers
     * @return int 消息ID，若失败返回 -1
     * @throws Throwable
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function push_queue_message(QueueMessageVo $message, array $receiveUsers = []): int
    {
        return container()
            ->get(SystemQueueLogService::class)
            ->pushMessage($message, $receiveUsers);
    }
}

if (!function_exists('add_queue')) {
    /**
     * 添加任务到队列
     * @param AmqpQueueVo $amqpQueueVo
     * @return bool
     * @throws Throwable
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function add_queue(AmqpQueueVo $amqpQueueVo): bool
    {
        return container()
            ->get(SystemQueueLogService::class)
            ->addQueue($amqpQueueVo);
    }
}
