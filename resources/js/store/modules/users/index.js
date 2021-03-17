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

    new_users_data: [],
    new_users_perPage: 10,
    new_users_total: 0,
    new_users_page: 1,
    new_users_loading: false,
}

const getters = {
    users_data: state => state.users_data,
    users_perPage: state => state.users_perPage,
    users_total: state => state.users_total,
    users_page: state => state.users_page,
    users_loading: state => state.users_loading,

    new_users_data: state => state.new_users_data,
    new_users_perPage: state => state.new_users_perPage,
    new_users_total: state => state.new_users_total,
    new_users_page: state => state.new_users_page,
    new_users_loading: state => state.new_users_loading,
}

const actions = {
    getUsersAction(context, option) {
        context.commit('setUsersLoadingHandler', true);
        Nprogress.start();
        axios.get(`${webServices.baseURL}/users?page=${option.page}&perPage=${option.perPage}`, { headers: { 'Content-Type': 'application/json' } })
            .then(response => {
                const { api_status, page, perPage, total, users } = response.data.response;
                if (api_status) {
                    context.commit('setUsersHandler', { page, perPage, total, users });
                } else {
                    context.commit('setUsersHandler', { page: 1, perPage: 10, total: 0, users: [] });
                }
            })
            .catch(error => {
                context.commit('setUsersHandler', { page: 1, perPage: 10, total: 0, users: [] });
                // console.log(error);
                // console.log("Failed");
            })
            .finally(() => {
                Nprogress.done();
            })
    },

    getNewUsersAction(context, option) {
        context.commit('setNewUsersLoadingHandler', true);
        Nprogress.start();
        axios.get(`${webServices.baseURL}/newusers?page=${option.page}&perPage=${option.perPage}`, { headers: { 'Content-Type': 'application/json' } })
            .then(response => {
                const { api_status, page, perPage, total, users } = response.data.response;
                if (api_status) {
                    context.commit('setNewUsersHandler', { page, perPage, total, users });
                } else {
                    context.commit('setNewUsersHandler', { page: 1, perPage: 10, total: 0, users: [] });
                }
            })
            .catch(error => {
                context.commit('setNewUsersHandler', { page: 1, perPage: 10, total: 0, users: [] });
                // console.log(error);
                // console.log("Failed");
            })
            .finally(() => {
                Nprogress.done();
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
        state.users_loading = false;
    },
    setUsersLoadingHandler(state, loading) {
        state.users_loading = loading;
    },

    setNewUsersHandler(state, payload) {
        const { page, perPage, total, users } = payload;
        state.new_users_page = page;
        state.new_users_perPage = perPage;
        state.new_users_total = total;
        state.new_users_data = users;
        state.new_users_loading = false;
    },
    setNewUsersLoadingHandler(state, loading) {
        state.new_users_loading = loading;
    },
}

export default {
    state,
    getters,
    actions,
    mutations
}