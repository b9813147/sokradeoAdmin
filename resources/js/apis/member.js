import req from './https';

// 定義接口
export const getData = (id, params) => req('get', `/group/member/${id}`, params)
export const deleteData = (id, params) => req('delete', `/group/member/${id}`, params)
export const postData = (params) => req('post', '/group/member', params)
export const putData = (id, params) => req('put', `/group/member/${id}`, params)
