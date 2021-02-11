import Vue from 'vue'
import Vuex from 'vuex'

// modules
import auth from './modules/auth';
import settings from './modules/settings';
import mail from './modules/mail';
import sidebar from './modules/sidebar';
import users from "./modules/users";
import trading from "./modules/trading";

Vue.use(Vuex);

export const store = new Vuex.Store({
    modules: {
        auth,
        settings,
        mail,
        sidebar,
        users,
        trading
    }
})
