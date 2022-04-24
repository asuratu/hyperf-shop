<?php

declare(strict_types=1);

namespace Api\Controller;

use Api\Request\Users\ShopUsersRegisterRequest;
use Api\Service\ShopUsersService;
use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 用户登录注册
 * Class ShopUsersController
 */
#[Controller(prefix: "api/v1/auth")]
class AuthController extends BaseController
{
    #[Inject]
    protected ShopUsersService $service;

    /**
     * 用户账号密码注册
     * @param ShopUsersRegisterRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("register")]
    public function register(ShopUsersRegisterRequest $request): ResponseInterface
    {
        $data = $request->inputs(['username', 'password', 'password_confirmation']);
        $data['login_ip'] = getClientIp($request);
        $data['login_time'] = Carbon::now();

        dump($data);

        return $this->success(['id' => $this->service->registerByAccount($data)]);
    }

}
