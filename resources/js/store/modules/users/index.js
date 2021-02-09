/**
 * Users Module
 */
import webServices from 'WebServices'
import Nprogress from 'nprogress';
import router from '../../../router';

const state = {
    users_data: [],
    users_perPage: 10,
    users_total: 0,
    users_page: 1,
}

const getters = {
    users_data: state => {
        return state.users_data;
    },
    users_perPage: state => {
        return state.users_perPage;
    },
    users_total: state => {
        return state.users_total;
    },
    users_page: state => {
        return state.users_page;
    },
}

const actions = {
    getUsersAction(context, page) {
        Nprogress.start();
        webServices.get(`/users?page=${page}`, { headers: { 'Content-Type': 'application/json' } })
            .then(response => {
                const { api_status, page, perPage, total, users } = response.data.response;
                if (api_status) {
                    Nprogress.done();
                    context.commit('setUsersHandler', { page, perPage, total, users });
                } else {
                    context.commit('setUsersHandler', { page: 1, perPage: 10, total: 0, users: [] });
                }
            })
            .catch(error => {
                context.commit('setUsersHandler', { page: 1, perPage: 10, total: 0, users: [] });
                console.log(error);
                console.log("Failed");
            })
    },
}

const mutations = {
    setUsersHandler(state, payload) {
        const { page, perPage, total, users } = payload;
        state.users_page = page;
        state.users_perPage = perPage;
        state.users_total = total;
        state.users_data = users;
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}