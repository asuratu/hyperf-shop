<?php

declare(strict_types=1);

namespace Api\Controller\V1;

use Api\Resource\ShopProductsResource;
use Api\Service\ShopProductsService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 商品管理控制器
 * Class ShopProductsController
 */
#[Controller(prefix: "api/v1/products")]
class ProductsController extends BaseController
{
    /**
     * 业务处理服务
     * ShopProductsService
     */
    #[Inject]
    protected ShopProductsService $service;

    /**
     * 列表
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("index")]
    public function index(): ResponseInterface
    {
        $list = $this->service->getPageList($this->request->all(), false);
        $list['items'] = ShopProductsResource::collection($list['items']);
        return $this->success($list);
    }

    /**
     * 读取数据
     * @param int $id
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("read/{id}")]
    public function read(int $id): ResponseInterface
    {
        return $this->success(new ShopProductsResource($this->service->read($id)));
    }

    /**
     * 收藏商品
     * @param int $id
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping("{id}/favorite"), Auth('api')]
    public function disfavor(int $id): ResponseInterface
    {
        $this->service->disfavor($id);
        return $this->success();
    }

    /**
     * 收藏商品
     * @param int $id
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("{id}/favorite"), Auth('api')]
    public function favor(int $id): ResponseInterface
    {
        $this->service->favor($id);
        return $this->success();
    }
}
