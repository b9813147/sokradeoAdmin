import Vue  from 'vue'
import Vuex from 'vuex'

import Auth     from './modules/auth';
import Path     from './modules/path';
import Status   from './modules/status';
import snackbar from "./modules/snackbar";

Vue.use(Vuex);

// MUTATIONS
const mutations = {};

// ACTIONS
const actions = {};


export default new Vuex.Store({
    mutations,
    actions,
    state  : {},
    modules: {
        Auth,
        Path,
        Status,
        snackbar
    }
});
