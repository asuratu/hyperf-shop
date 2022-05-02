import { request } from '@/utils/request.js'

/**
 * 商品管理 API JS
 */

export default {

  /**
   * 获取商品管理分页列表
   * @returns
   */
  getList (params = {}) {
    return request({
      url: 'shop/products/index',
      method: 'get',
      params
    })
  },

  /**
    * 获取商品管理选择树 (树表才生效)
    * @returns
    */
  tree () {
    return request({
      url: 'shop/products/tree',
      method: 'get'
    })
  },

  /**
   * 从回收站获取商品管理数据列表
   * @returns
   */
  getRecycleList (params = {}) {
    return request({
      url: 'shop/products/recycle',
      method: 'get',
      params
    })
  },

  /**
   * 添加商品管理
   * @returns
   */
  save (params = {}) {
    return request({
      url: 'shop/products/save',
      method: 'post',
      data: params
    })
  },

  /**
   * 读取商品管理
   * @returns
   */
  read (params = {}) {
    return request({
      url: 'shop/products/read',
      method: 'post',
      data: params
    })
  },

  /**
   * 将商品管理移到回收站
   * @returns
   */
  deletes (ids) {
    return request({
      url: 'shop/products/delete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 恢复商品管理数据
   * @returns
   */
  recoverys (ids) {
    return request({
      url: 'shop/products/recovery/' + ids,
      method: 'put'
    })
  },

  /**
   * 真实删除商品管理
   * @returns
   */
  realDeletes (ids) {
    return request({
      url: 'shop/products/realDelete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 更新商品管理数据
   * @returns
   */
  update (id, params = {}) {
    return request({
      url: 'shop/products/update/' + id,
      method: 'put',
      data: params
    })
  },

  /**
     * 商品管理统计信息
     * @returns
  */
  getTabNum (params = {}) {
  	return request({
  	  url: 'shop/products/getTabNum',
  	  method: 'post',
  	  data: params
  	})
  },

}