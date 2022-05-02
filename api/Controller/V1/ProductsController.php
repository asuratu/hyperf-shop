<?php

declare(strict_types=1);

namespace Api\Controller\V1;

use App\Shop\Service\ShopProductsService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\Annotation\Permission;
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
    #[GetMapping("index"), Permission("shop:products:index")]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }
}
