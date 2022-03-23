import axios                     from 'axios';
import router                    from '../router/index.js'
import {tip, to403Page, toLogin} from "./utils";

/**
 * 請求失敗統一處理
 * @param {Number} status 請求失敗狀態碼
 */

const errorHandle = (status, msg) => {
    switch (status) {
        // 400 : 登入失敗
        case 400:
            tip(msg);
            break;
        // 401: backend session 過期 => 移到check login 去判斷
        case 401:
            tip('登入過期，重新登入')
            setTimeout(() => {
                toLogin();
            }, 1000);
            break;
        //403: 權限不足
        case 403:
            to403Page();
            break;
        //404 : 請求失敗
        case 404:
            tip(msg);
            break;
        default:
            console.log('res 沒有攔截到錯誤:' + msg)
    }
};

// axios 的實例
var instance = axios.create({
    baseURL: '/api/'
});

// request 攔截器
instance.interceptors.request.use((config) => {
        return config;
    },
    (error) => {
        return Promise.reject(error);
    });

// response 攔截器
instance.interceptors.response.use((response) => {
    return response;
}, (error) => {
    const {response} = error;
    if (response) {
        // 成功發出請求且收到 response, 但有error
        errorHandle(response.status, response.data.error)
        return Promise.reject(error);
    } else {
        // 成功發出請求但沒收到response
        if (!window.navigator.onLine) {
            // 如果是網路斷線
            tip('網路出了點問題，請刷新頁面')
        } else {
            // 可能是跨域,或程式問題
            return Promise.reject(error);
        }
    }
});

export default function (method, url, data = null) {
    method = method.toLowerCase();
    switch (method) {
        case 'post':
            return instance.post(url, data);
        case 'get':
            return instance.get(url, {params: data});
        case 'put':
            return instance.put(url, data);
        case 'delete':
            return instance.delete(url, data);
        default:
            console.log('Invalid method')
            return false;
    }

}
