import router from '../router/index.js';

export const tip = msg => {
    console.log(msg);
}
/**
 * 跳轉回首頁
 * 攜帶當前的頁面路由,登入完成後跳轉回原本頁面
 */
export const toLogin = () => {
    router.replace({
        name : 'Login',
        query: {
            redirect: router.currentRoute.fullPath
        }
    })
};
/**
 * 跳轉到403 error page
 */
export const to403Page = () => {
    router.replace({
        name: '403'
    })
}

