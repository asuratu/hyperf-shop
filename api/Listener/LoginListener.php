<?php

declare(strict_types=1);

namespace Api\Listener;

use Api\Model\User;
use App\System\Service\SystemLoginLogService;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Mine\Event\ApiUserLoginAfter;
use Mine\Helper\Str;
use Mine\MineRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class LoginListener
 */
#[Listener]
class LoginListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            ApiUserLoginAfter::class
        ];
    }

    /**
     * @param ApiUserLoginAfter $event
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(object $event)
    {
        $request = container()->get(MineRequest::class);
        $service = container()->get(SystemLoginLogService::class);
        $redis = redis();

        $agent = $request->getHeader('user-agent')[0];
        $ip = $request->ip();
        $service->save([
            'username' => $event->userinfo['username'],
            'ip' => $ip,
            'ip_location' => Str::ipToRegion($ip),
            'os' => $this->os($agent),
            'browser' => $this->browser($agent),
            'status' => !$event->loginStatus,
            'message' => $event->message,
            'login_time' => date('Y-m-d H:i:s'),
            'type' => 1,
        ]);

        $key = sprintf("%sToken:%s", config('cache.default.prefix'), $event->userinfo['id']);

        $redis->exists($key) && $redis->del($key);
        ($event->loginStatus && $event->token) && $redis->set($key, $event->token, config('jwt.ttl'));

        if ($event->loginStatus) {
            $event->userinfo['login_ip'] = $ip;
            $event->userinfo['login_time'] = date('Y-m-d H:i:s');

            User::query()->where('id', $event->userinfo['id'])->update([
                'login_ip' => $ip,
                'login_time' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * @param $agent
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function os($agent): string
    {
        if (false !== stripos($agent, 'win') && preg_match('/nt 6.1/i', $agent)) {
            return 'Windows 7';
        }
        if (false !== stripos($agent, 'win') && preg_match('/nt 6.2/i', $agent)) {
            return 'Windows 8';
        }
        if (false !== stripos($agent, 'win') && preg_match('/nt 10.0/i', $agent)) {
            return 'Windows 10';
        }
        if (false !== stripos($agent, 'win') && preg_match('/nt 11.0/i', $agent)) {
            return 'Windows 11';
        }
        if (false !== stripos($agent, 'win') && preg_match('/nt 5.1/i', $agent)) {
            return 'Windows XP';
        }
        if (false !== stripos($agent, 'linux')) {
            return 'Linux';
        }
        if (false !== stripos($agent, 'mac')) {
            return 'Mac';
        }
        return t('jwt.unknown');
    }

    /**
     * @param $agent
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function browser($agent): string
    {
        if (false !== stripos($agent, "MSIE")) {
            return 'MSIE';
        }
        if (false !== stripos($agent, "Edg")) {
            return 'Edge';
        }
        if (false !== stripos($agent, "Chrome")) {
            return 'Chrome';
        }
        if (false !== stripos($agent, "Firefox")) {
            return 'Firefox';
        }
        if (false !== stripos($agent, "Safari")) {
            return 'Safari';
        }
        if (false !== stripos($agent, "Opera")) {
            return 'Opera';
        }
        return t('jwt.unknown');
    }
}
