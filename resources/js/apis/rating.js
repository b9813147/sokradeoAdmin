import req from './https.js';

// 定義接口
export const apiGetRatings = (id, params) => req('get', `/group/rating/${id}`, params)
export const apiDeleteRating = (id, params) => req('delete', `/group/rating/${id}`, params)
export const apiCreateRating = (params) => req('post', '/group/rating', params)
export const apiUpdateRating = (id, params) => req('put', `/group/rating/${id}`, params)
export const apiUpdateSortRating = (id, params) => req('put', `/group/rating/sort/${id}`, params)
