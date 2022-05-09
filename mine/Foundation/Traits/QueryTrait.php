<?php

namespace Mine\Foundation\Traits;

/**
 * 查询基类
 * Trait QueryTrait
 * @package Mine\Foundation\Traits
 */
trait QueryTrait
{
    /**
     * 处理分页条件
     *
     * @param $query
     * @param $params
     * @return mixed
     */
    public function pagingCondition($query, $params): mixed
    {
        $cur_page = $params['cur_page'] ?? 1;
        $page_size = $params['page_size'] ?? 20;

        $offset = ($cur_page - 1) * $page_size;
        $limit = $page_size;
        return $query->offset($offset)->limit($limit);
    }
}