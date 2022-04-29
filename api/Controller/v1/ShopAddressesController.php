<?php

declare(strict_types=1);

namespace Api\Controller\v1;

use Api\Resource\ShopAddressCollection;
use Api\Resource\ShopAddressResource;
use Api\Service\ShopAddressesService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 收货地址管理控制器
 * Class ShopAddressesController
 */
#[Controller(prefix: "api/v1/addresses"), Auth('api')]
class ShopAddressesController extends MineController
{
    /**
     * 业务处理服务
     * ShopAddressesService
     */
    #[Inject]
    protected ShopAddressesService $service;

    /**
     * 列表
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("index")]
    public function index(): ResponseInterface
    {
        $list = $this->service->getMyPageList(user('api')->getId(), $this->request->all());
        $list['items'] = ShopAddressResource::collection($list['items']);
        return $this->success($list);
    }

    /**
     * 新增
     * @param ShopAddressesCreateRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("save"), Permission("shop:addresses:save"), OperationLog]
    public function save(ShopAddressesCreateRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 读取数据
     * @param int $id
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("read/{id}"), Permission("shop:addresses:read")]
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 更新
     * @param int $id
     * @param ShopAddressesUpdateRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping("update/{id}"), Permission("shop:addresses:update"), OperationLog]
    public function update(int $id, ShopAddressesUpdateRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量删除数据到回收站
     * @param String $ids
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping("delete/{ids}"), Permission("shop:addresses:delete"), OperationLog]
    public function delete(string $ids): ResponseInterface
    {
        return $this->service->delete($ids) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量真实删除数据 （清空回收站）
     * @param String $ids
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping("realDelete/{ids}"), Permission("shop:addresses:realDelete"), OperationLog]
    public function realDelete(string $ids): ResponseInterface
    {
        return $this->service->realDelete($ids) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量恢复在回收站的数据
     * @param String $ids
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping("recovery/{ids}"), Permission("shop:addresses:recovery"), OperationLog]
    public function recovery(string $ids): ResponseInterface
    {
        return $this->service->recovery($ids) ? $this->success() : $this->error();
    }

    /**
     * 获取tabs统计数据
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("getTabNum")]
    public function getTabNum(): ResponseInterface
    {
        return $this->success($this->service->getTabNum($this->request->input('key')));
    }
}
