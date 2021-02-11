/**
 * Accounts Module
 */
import webServices from 'WebServices';
import axios from "axios";
import Nprogress from 'nprogress';
import router from '../../../router';

const state = {
    accounts_data: [],
    accounts_perPage: 10,
    accounts_total: 0,
    accounts_page: 1,
    accounts_loading: false,
}

const getters = {
    accounts_data: state => {
        return state.accounts_data;
    },
    accounts_perPage: state => {
        return state.accounts_perPage;
    },
    accounts_total: state => {
        return state.accounts_total;
    },
    accounts_page: state => {
        return state.accounts_page;
    },
    accounts_loading: state => state.accounts_loading,
}

const actions = {
    getAccountsAction(context, option) {
        context.commit('setAccountsLoadingHandler', false);
        Nprogress.start();
        axios.get(`${webServices.baseURL}/accounts?page=${option.page}&perPage=${option.perPage}`, { headers: { 'Content-Type': 'application/json' } })
            .then(response => {
                const { api_status, page, perPage, total, accounts } = response.data.response;
                if (api_status) {
                    Nprogress.done();
                    context.commit('setAccountsHandler', { page, perPage, total, accounts });
                } else {
                    context.commit('setAccountsHandler', { page: 1, perPage: 10, total: 0, accounts: [] });
                }
            })
            .catch(error => {
                Nprogress.done();
                context.commit('setAccountsHandler', { page: 1, perPage: 10, total: 0, accounts: [] });
                console.log(error);
                console.log("Failed");
            })
    },
}

const mutations = {
    setAccountsHandler(state, payload) {
        const { page, perPage, total, accounts } = payload;
        state.accounts_page = page;
        state.accounts_perPage = perPage;
        state.accounts_total = total;
        state.accounts_data = accounts;
        state.loading = false;
    },
    setAccountsLoadingHandler(state, loading) {
        state.loading = loading;
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}