<?php

declare(strict_types=1);

namespace Api\Controller\V1;

use Api\Request\Users\UserLoginRequest;
use Api\Request\Users\UserRegisterRequest;
use Api\Resource\UserLoginResource;
use Api\Resource\UserResource;
use Api\Service\UsersService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Helper\LoginUser;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * 用户登录注册
 * Class UsersController
 */
#[Controller(prefix: "api/v1/auth")]
class AuthController extends BaseController
{
    #[Inject]
    protected UsersService $service;

    /**
     * 用户账号密码登录
     * @param UserLoginRequest $request
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("login")]
    public function login(UserLoginRequest $request): ResponseInterface
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
     * @param UserRegisterRequest $request
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("register")]
    public function register(UserRegisterRequest $request): ResponseInterface
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
        return $this->success(new UserResource($this->service->getInfo()));
    }

    /**
     * 退出登录
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

    /**
     * 刷新token
     * @param LoginUser $user
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    #[PostMapping("refresh")]
    public function refresh(LoginUser $user): ResponseInterface
    {
        return $this->success(['token' => $user->refresh()]);
    }
}
