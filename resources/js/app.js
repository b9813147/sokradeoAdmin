require('./bootstrap');
import Vue                from 'vue'
import i18n               from './lang/index'
import Vuetify            from 'vuetify';
import store              from './store/index'
import router             from './router/index'
import 'material-design-icons-iconfont/dist/material-design-icons.css'
import Vuelidate          from 'vuelidate'
import snackbarPlugin     from "./plugins/snackbar";
import VueQRCodeComponent from 'vue-qrcode-component'

window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + store._modules.root.state.Auth.user.token;
Vue.use(Vuelidate);
Vue.use(require('vue-moment'))
Vue.use(snackbarPlugin, {store});
Vue.component('qr-code', VueQRCodeComponent)
import App                from './views/App'

Vue.use(Vuetify);
Vue.config.productionTip = false;

const isDebug_mode = process.env.NODE_ENV !== 'production';
Vue.config.debug = isDebug_mode;
Vue.config.devtools = isDebug_mode;

new Vue({
    el     : '#app',
    i18n,
    store,
    router,
    vuetify: new Vuetify(),
    render : h => h(App),
});


