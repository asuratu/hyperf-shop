<?php

declare(strict_types=1);

namespace Api\Controller\v1;

use Api\Request\Users\ShopUserLoginRequest;
use Api\Request\Users\ShopUserRegisterRequest;
use Api\Resource\ShopUserResource;
use Api\Resource\UserLoginResource;
use Api\Service\ShopUsersService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\InvalidArgumentException;

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
     * 用户账号密码登录
     * @param ShopUserLoginRequest $request
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("login")]
    public function login(ShopUserLoginRequest $request): ResponseInterface
    {
        return $this->success(
            new UserLoginResource(
                $this->service->login(
                    $request->inputs([
                        'username',
                        'password'
                    ])
                )
            )
        );
    }

    /**
     * 用户账号密码注册
     * @param ShopUserRegisterRequest $request
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("register")]
    public function register(ShopUserRegisterRequest $request): ResponseInterface
    {
        return $this->success(
            new UserLoginResource(
                $this->service->registerByAccount(
                    $request->inputs([
                        'username',
                        'password',
                        'password_confirmation'
                    ])
                )
            )
        );
    }

    /**
     * 用户信息
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("getInfo"), Auth('api')]
    public function getInfo(): ResponseInterface
    {
        return $this->success(new ShopUserResource($this->service->getInfo()));
    }

    /**
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    #[PostMapping("logout"), Auth('api')]
    public function logout(): ResponseInterface
    {
        $this->service->logout();
        return $this->success();
    }
}
