/**
 * Users Module
 */
import webServices from 'WebServices';
import axios from "axios";
import Nprogress from 'nprogress';
import router from '../../../router';

const state = {
    users_data: [],
    users_perPage: 10,
    users_total: 0,
    users_page: 1,
    users_loading: false,
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
    users_loading: state => state.users_loading,
}

const actions = {
    getUsersAction(context, option) {
        context.commit('setUsersLoadingHandler', false);
        Nprogress.start();
        axios.get(`${webServices.baseURL}/users?page=${option.page}&perPage=${option.perPage}`, { headers: { 'Content-Type': 'application/json' } })
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
                Nprogress.done();
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
        state.loading = false;
    },
    setUsersLoadingHandler(state, loading) {
        state.loading = loading;
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}