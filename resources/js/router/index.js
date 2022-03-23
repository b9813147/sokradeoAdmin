import Vue       from 'vue';
import VueRouter from "vue-router";

Vue.use(VueRouter);
// 解決編程式路由往同一地址跳轉時會報錯的情況
const originalPush = VueRouter.prototype.push
const originalReplace = VueRouter.prototype.replace
VueRouter.prototype.push = function push(location, onResolve, onReject) {
    if (onResolve || onReject) return originalPush.call(this, location, onResolve, onReject)
    return originalPush.call(this, location).catch(err => err)
}


VueRouter.prototype.replace = function push(location, onResolve, onReject) {
    if (onResolve || onReject) return originalReplace.call(this, location, onResolve, onReject)
    return originalReplace.call(this, location).catch(err => err)
}
const routes = [
    {
        path     : '/',
        name     : 'dashboard',
        component: () => import('../components/pages/dashboard')
    },
    {
        path     : '/group/index',
        name     : 'group',
        component: () => import('../components/group/Group.vue')
    },
    {
        path     : '/group/user',
        name     : 'groupUser',
        component: () => import('../components/group/GroupUser.vue')
    },
    {
        path     : '/group/channel',
        name     : 'channel',
        component: () => import('../components/group/GroupChannel.vue')
    },
    {
        path     : '/group/subject',
        name     : 'subject',
        component: () => import('../components/group/Subject')
    },
    {
        path     : '/group/rating',
        name     : 'rating',
        component: () => import('../components/group/GroupRating')
    },
    {
        path     : '/group/tag',
        name     : 'tag',
        component: () => import('../components/group/Tag')
    },
    {
        path     : '/group/notification',
        name     : 'AppNotification',
        component: () => import('../components/app/Notification.vue')
    },
    // 學區相關路由
    {
        path     : '/district',
        name     : 'district',
        component: () => import('../components/district/District.vue')
    },

    {
        path     : '/district/user',
        name     : 'districtUser',
        component: () => import('../components/district/DistrictUser.vue')
    },
    {
        path     : '/district/channel',
        name     : 'districtChannel',
        component: () => import('../components/district/DistrictChannel.vue')
    },
    {
        path     : '/district/subject',
        name     : 'districtSubject',
        component: () => import('../components/district/DistrictSubject.vue')
    },
    {
        path     : '/district/classification',
        name     : 'classification',
        component: () => import('../components/district/Classification.vue')
    },
    {
        path     : '/district/rating',
        name     : 'districtRating',
        component: () => import('../components/district/DistrictRating.vue')
    },
    {
        path     : '/division/index',
        name     : 'division',
        component: () => import('../components/division/index.vue')
    },
    //全站相關路由
    {
        path     : '/global/recommendedvideo',
        name     : 'globalRecommendedVideo',
        component: () => import('../components/global/GlobalRecommendedVideo.vue')
    },
    {
        path     : '/global/notification',
        name     : 'notification',
        component: () => import('../components/global/GlobalNotification')
    },
    {
        path     : '/global/district',
        name     : 'district_manage',
        component: () => import('../components/global/GlobalDistrict')
    },
    {
        path     : '/global/event',
        name     : 'event',
        component: () => import('../components/global/event.vue')
    },
    {
        path     : '/global/group',
        name     : 'global_group',
        component: () => import('../components/global/group.vue')
    },

];

const router = new VueRouter({
    mode: 'history',
    routes
});

export default router;
