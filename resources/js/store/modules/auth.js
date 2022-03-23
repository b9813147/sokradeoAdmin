export default {
    state  : {
        user : {
            id              : config.userInfo.user_id,
            group_id        : config.userInfo.group_id,
            channel_id      : config.userInfo.channel_id,
            district_id     : config.userInfo.districts_id,
            public          : config.userInfo.public,
            member_duty     : config.userInfo.member_duty,
            member_status   : config.userInfo.member_status,
            global_role_type: config.userInfo.global_role_type,
            token           : config.userInfo.token,
            thumbnail       : config.userInfo.thumbnail,
            name            : config.userInfo.name,
        },
        login: {
            status : config.status,
            message: config.message
        },
    },
    getters: {},

    mutations: {},
    actions  : {}
};
