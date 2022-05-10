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

namespace Mine\Traits;

use Closure;
use Hyperf\Database\Model\Collection;
use Hyperf\Utils\HigherOrderTapProxy;
use Mine\Abstracts\AbstractMapper;
use Mine\Annotation\Transaction;
use Mine\ApiModel;
use Mine\Constants\StatusCode;
use Mine\Exception\BusinessException;
use Mine\Exception\NormalStatusException;
use Mine\MineCollection;
use Mine\MineModel;
use Mine\MineResponse;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

trait ServiceTrait
{
    /**
     * @var AbstractMapper
     */
    public $mapper;

    /**
     * 从回收站过去列表数据
     * @param array|null $params
     * @param bool $isScope
     * @return array
     */
    public function getListByRecycle(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = true;
        return $this->mapper->getList($params, $isScope);
    }

    /**
     * 获取列表数据
     * @param array|null $params
     * @param bool $isScope
     * @return array
     */
    public function getList(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = false;
        return $this->mapper->getList($params, $isScope);
    }

    /**
     * 获取用户相关的列表数据（带分页）
     * @param int|string $id
     * @param array|null $params
     * @param bool $isScope
     * @param string $foreignKey
     * @return array
     */
    public function getMyPageList(int|string $id, ?array $params = null, bool $isScope = true, string $foreignKey = 'user_id'): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        return $this->mapper->getMyPageList($id, $params, $isScope, $foreignKey);
    }

    /**
     * 从回收站获取列表数据（带分页）
     * @param array|null $params
     * @param bool $isScope
     * @return array
     */
    public function getPageListByRecycle(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = true;
        return $this->mapper->getPageList($params, $isScope);
    }

    /**
     * 获取列表数据（带分页）
     * @param array|null $params
     * @param bool $isScope
     * @return array
     */
    public function getPageList(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }

        return $this->mapper->getPageList($params, $isScope);
    }

    /**
     * 从回收站获取树列表
     * @param array|null $params
     * @param bool $isScope
     * @return array
     */
    public function getTreeListByRecycle(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = true;
        return $this->mapper->getTreeList($params, $isScope);
    }

    /**
     * 获取树列表
     * @param array|null $params
     * @param bool $isScope
     * @return array
     */
    public function getTreeList(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = false;
        return $this->mapper->getTreeList($params, $isScope);
    }

    /**
     * 新增外键相关数据
     * @param array $data
     * @param string $foreignKey
     * @param string|int|null $foreignId
     * @return int
     */
    public function mySave(array $data, string $foreignKey = 'user_id', string|int|null $foreignId = null): int
    {
        if (empty($foreignId)) {
            $foreignId = user('api')->getId();
        }

        $data[$foreignKey] = $foreignId;
        return $this->save($data);
    }

    /**
     * 新增数据
     * @param array $data
     * @return int
     */
    public function save(array $data): int
    {
        return $this->mapper->save($data);
    }

    /**
     * 批量新增
     * @param array $collects
     * @Transaction
     * @return bool
     */
    public function batchSave(array $collects): bool
    {
        foreach ($collects as $collect) {
            $this->mapper->save($collect);
        }
        return true;
    }

    /**
     * 读取用户相关的一条数据
     * @param int $id
     * @return MineModel|ApiModel|null
     */
    public function myRead(int $id): MineModel|ApiModel|null
    {
        return $this->mapper->myRead($id, user('api')->getId());
    }

    /**
     * 读取一条数据
     * @param int $id
     * @return MineModel|ApiModel|null
     */
    public function read(int $id): MineModel|ApiModel|null
    {
        return $this->mapper->read($id);
    }

    /**
     * Description:获取单个值
     * User:mike
     * @param array $condition
     * @param string $columns
     * @return HigherOrderTapProxy|mixed|void|null
     */
    public function value(array $condition, string $columns = 'id')
    {
        return $this->mapper->value($condition, $columns);
    }

    /**
     * Description:获取单列值
     * User:mike
     * @param array $condition
     * @param string $columns
     * @return array|null
     */
    public function pluck(array $condition, string $columns = 'id'): array
    {
        return $this->mapper->pluck($condition, $columns);
    }

    /**
     * 从回收站读取一条数据
     * @param int $id
     * @return MineModel|ApiModel
     * @noinspection PhpUnused
     */
    public function readByRecycle(int $id): MineModel|ApiModel
    {
        return $this->mapper->readByRecycle($id);
    }

    /**
     * 单个或批量软删除当前用户的数据
     * @param string $ids
     * @param string $foreignKey
     * @param string|int|null $foreignId
     * @return bool
     * @throws NormalStatusException
     */
    public function myDelete(string $ids, string $foreignKey = 'user_id', string|int|null $foreignId = null): bool
    {
        if (empty($ids)) {
            return true;
        }
        if (empty($foreignId)) {
            $foreignId = user('api')->getId();
        }
        $idArr = explode(',', $ids);
        // 检查是否存在不是当前用户的数据
        $check = $this->mapper->exists(function ($query) use ($idArr, $foreignKey, $foreignId) {
            $query->whereIn('id', $idArr)->where($foreignKey, '<>', $foreignId);
        });
        $check && throw new BusinessException(StatusCode::ERR_NOT_PERMISSION);
        return $this->mapper->delete($idArr);
    }

    /**
     * 单个或批量软删除数据
     * @param string $ids
     * @return bool
     */
    public function delete(string $ids): bool
    {
        return !empty($ids) && $this->mapper->delete(explode(',', $ids));
    }

    /**
     * 更新一条数据
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        return $this->mapper->update($id, $data);
    }

    /**
     * 更新当前用户相关的一条数据
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function myUpdate(int $id, array $data): bool
    {
        return $this->mapper->myUpdate($id, $data, user('api')->getId());
    }


    /**
     * 按条件更新数据
     * @param array $condition
     * @param array $data
     * @return bool
     */
    public function updateByCondition(array $condition, array $data): bool
    {
        return $this->mapper->updateByCondition($condition, $data);
    }

    /**
     * 单个或批量真实删除数据
     * @param string $ids
     * @return bool
     */
    public function realDelete(string $ids): bool
    {
        return !empty($ids) && $this->mapper->realDelete(explode(',', $ids));
    }

    /**
     * 单个或批量从回收站恢复数据
     * @param string $ids
     * @return bool
     */
    public function recovery(string $ids): bool
    {
        return !empty($ids) && $this->mapper->recovery(explode(',', $ids));
    }

    /**
     * 修改数据状态
     * @param int $id
     * @param string $value
     * @return bool
     */
    public function changeStatus(int $id, string $value): bool
    {
        if ($value === '0') {
            $this->mapper->enable([$id]);
            return true;
        } elseif ($value === '1') {
            $this->mapper->disable([$id]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 单个或批量启用数据
     * @param string $ids
     * @param string $field
     * @return bool
     */
    public function enable(string $ids, string $field = 'status'): bool
    {
        return !empty($ids) && $this->mapper->enable(explode(',', $ids), $field);
    }

    /**
     * 单个或批量禁用数据
     * @param string $ids
     * @param string $field
     * @return bool
     */
    public function disable(string $ids, string $field = 'status'): bool
    {
        return !empty($ids) && $this->mapper->disable(explode(',', $ids), $field);
    }

    /**
     * 导出数据
     * @param array $params
     * @param string|null $dto
     * @param string|null $filename
     * @return ResponseInterface
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function export(array $params, ?string $dto, string $filename = null): ResponseInterface
    {
        if (empty($dto)) {
            return container()->get(MineResponse::class)->error('导出未指定DTO');
        }

        if (empty($filename)) {
            $filename = $this->mapper->getModel()->getTable();
        }

        return (new MineCollection())->export($dto, $filename, $this->mapper->getList($params));
    }

    /**
     * 数据导入
     * @param string $dto
     * @param Closure|null $closure
     * @param bool $isExportErrorData
     * @return bool
     * @Transaction
     */
    public function import(string $dto, ?Closure $closure = null, bool $isExportErrorData = false): bool
    {
        return $this->mapper->import($dto, $closure, $isExportErrorData);
    }

    /**
     * 数组数据转分页数据显示
     * @param array|null $params
     * @param string $pageName
     * @return array
     */
    public function getArrayToPageList(?array $params = [], string $pageName = 'page'): array
    {
        $collect = $this->handleArraySearch(collect($this->getArrayData($params)), $params);

        $pageSize = MineModel::PAGE_SIZE;
        $page = 1;

        if ($params[$pageName] ?? false) {
            $page = (int)$params[$pageName];
        }

        if ($params['pageSize'] ?? false) {
            $pageSize = (int)$params['pageSize'];
        }

        $data = $collect->forPage($page, $pageSize)->toArray();

        return [
            'items' => $this->getCurrentArrayPageBefore($data, $params),
            'pageInfo' => [
                'total' => $collect->count(),
                'currentPage' => $page,
                'totalPage' => ceil($collect->count() / $pageSize)
            ]
        ];
    }

    /**
     * 数组数据搜索器
     * @param \Hyperf\Utils\Collection $collect
     * @param array $params
     * @return Collection
     */
    protected function handleArraySearch(\Hyperf\Utils\Collection $collect, array $params): \Hyperf\Utils\Collection
    {
        return $collect;
    }

    /**
     * 设置需要分页的数组数据
     * @param array $params
     * @return array
     */
    protected function getArrayData(array $params = []): array
    {
        return [];
    }

    /**
     * 数组当前页数据返回之前处理器，默认对key重置
     * @param array $data
     * @param array $params
     * @return array
     */
    protected function getCurrentArrayPageBefore(array &$data, array $params = []): array
    {
        sort($data);
        return $data;
    }

    /**
     * 获取tabs数据统计
     * @param string $field
     * @return array
     */
    public function getTabNum(string $field): array
    {
        return $this->mapper->getTabNum($field);
    }
}
