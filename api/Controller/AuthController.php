<?php

declare(strict_types=1);

namespace Api\Controller;

use Api\Request\Users\ShopUsersRegisterRequest;
use Api\Service\ShopUsersService;
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
        return $this->success($this->service->registerByAccount($request->inputs(['username', 'password', 'password_confirmation'])));
    }

}
