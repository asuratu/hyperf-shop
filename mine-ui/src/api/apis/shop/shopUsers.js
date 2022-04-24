import { request } from '@/utils/request.js'

/**
 * 用户管理 API JS
 */

export default {

  /**
   * 获取用户管理分页列表
   * @returns
   */
  getList (params = {}) {
    return request({
      url: 'shop/users/index',
      method: 'get',
      params
    })
  },

  /**
    * 获取用户管理选择树 (树表才生效)
    * @returns
    */
  tree () {
    return request({
      url: 'shop/users/tree',
      method: 'get'
    })
  },

  /**
   * 从回收站获取用户管理数据列表
   * @returns
   */
  getRecycleList (params = {}) {
    return request({
      url: 'shop/users/recycle',
      method: 'get',
      params
    })
  },

  /**
   * 添加用户管理
   * @returns
   */
  save (params = {}) {
    return request({
      url: 'shop/users/save',
      method: 'post',
      data: params
    })
  },

  /**
   * 读取用户管理
   * @returns
   */
  read (params = {}) {
    return request({
      url: 'shop/users/read',
      method: 'post',
      data: params
    })
  },

  /**
   * 将用户管理移到回收站
   * @returns
   */
  deletes (ids) {
    return request({
      url: 'shop/users/delete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 恢复用户管理数据
   * @returns
   */
  recoverys (ids) {
    return request({
      url: 'shop/users/recovery/' + ids,
      method: 'put'
    })
  },

  /**
   * 真实删除用户管理
   * @returns
   */
  realDeletes (ids) {
    return request({
      url: 'shop/users/realDelete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 更新用户管理数据
   * @returns
   */
  update (id, params = {}) {
    return request({
      url: 'shop/users/update/' + id,
      method: 'put',
      data: params
    })
  },

  /**
     * 用户管理统计信息
     * @returns
  */
  getTabNum (params = {}) {
  	return request({
  	  url: 'shop/users/getTabNum',
  	  method: 'post',
  	  data: params
  	})
  },

}