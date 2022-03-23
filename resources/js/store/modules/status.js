export default {
    state: {
        isLoading: false,
        isAlert: false,
        isSuccess: false
    },
    getters: {},

    // ajax 或 運算在此處理 記住這裡不做改狀態的事情！！
    actions: {
        updateLoading(context, status) {
            context.commit('LOADING', status);
        },
        updateAlert(context, status) {
            context.commit('ALERT', status);
            // setTimeout(context.commit('ALERT', false), 3000);
        },
        // timeoutAlert(context, status) {
        //     setTimeout(() => {
        //         context.commit('ALERT', status);
        //     }, 3000);
        // }


    },
    // 修改狀態在此物件 處理
    mutations: {
        LOADING(state, status) {
            state.isLoading = status;
        },

        ALERT(state, status) {
            state.isAlert = status;
        }
    },

}
