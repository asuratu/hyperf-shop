<?php

declare(strict_types=1);

namespace Api\Controller\V1;

use Api\Request\Users\AddressesRequest;
use Api\Resource\AddressResource;
use Api\Service\AddressesService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 收货地址管理控制器
 * Class AddressesController
 */
#[Controller(prefix: "api/v1/addresses"), Auth('api')]
class AddressesController extends MineController
{
    #[Inject]
    protected AddressesService $service;

    /**
     * 列表
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("index")]
    public function index(): ResponseInterface
    {
        $list = $this->service->getMyPageList(user('api')->getId(), $this->request->all(), false);
        $list['items'] = AddressResource::collection($list['items']);
        return $this->success($list);
    }

    /**
     * 新增
     * @param AddressesRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("save")]
    public function save(AddressesRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->mySave($request->all())]);
    }

    /**
     * 更新
     * @param int $id
     * @param AddressesRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping("update/{id}")]
    public function update(int $id, AddressesRequest $request): ResponseInterface
    {
        return $this->service->myUpdate($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量删除数据到回收站
     * @param String $ids
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping("delete/{ids}")]
    public function delete(string $ids): ResponseInterface
    {
        return $this->service->myDelete($ids) ? $this->success() : $this->error();
    }
}
