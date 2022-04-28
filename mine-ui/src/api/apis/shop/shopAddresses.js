import { request } from '@/utils/request.js'

/**
 * 收货地址管理 API JS
 */

export default {

  /**
   * 获取收货地址管理分页列表
   * @returns
   */
  getList (params = {}) {
    return request({
      url: 'shop/addresses/index',
      method: 'get',
      params
    })
  },

  /**
    * 获取收货地址管理选择树 (树表才生效)
    * @returns
    */
  tree () {
    return request({
      url: 'shop/addresses/tree',
      method: 'get'
    })
  },

  /**
   * 从回收站获取收货地址管理数据列表
   * @returns
   */
  getRecycleList (params = {}) {
    return request({
      url: 'shop/addresses/recycle',
      method: 'get',
      params
    })
  },

  /**
   * 添加收货地址管理
   * @returns
   */
  save (params = {}) {
    return request({
      url: 'shop/addresses/save',
      method: 'post',
      data: params
    })
  },

  /**
   * 读取收货地址管理
   * @returns
   */
  read (params = {}) {
    return request({
      url: 'shop/addresses/read',
      method: 'post',
      data: params
    })
  },

  /**
   * 将收货地址管理移到回收站
   * @returns
   */
  deletes (ids) {
    return request({
      url: 'shop/addresses/delete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 恢复收货地址管理数据
   * @returns
   */
  recoverys (ids) {
    return request({
      url: 'shop/addresses/recovery/' + ids,
      method: 'put'
    })
  },

  /**
   * 真实删除收货地址管理
   * @returns
   */
  realDeletes (ids) {
    return request({
      url: 'shop/addresses/realDelete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 更新收货地址管理数据
   * @returns
   */
  update (id, params = {}) {
    return request({
      url: 'shop/addresses/update/' + id,
      method: 'put',
      data: params
    })
  },

  /**
     * 收货地址管理统计信息
     * @returns
  */
  getTabNum (params = {}) {
  	return request({
  	  url: 'shop/addresses/getTabNum',
  	  method: 'post',
  	  data: params
  	})
  },

}