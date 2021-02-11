/**
 * Accounts Module
 */
import webServices from 'WebServices';
import axios from "axios";
import Nprogress from 'nprogress';

const state = {
    accounts_data: [],
    accounts_perPage: 10,
    accounts_total: 0,
    accounts_page: 1,
    accounts_loading: false,

    provideSignal_data: [],
    provideSignal_perPage: 10,
    provideSignal_total: 0,
    provideSignal_page: 1,
    provideSignal_loading: false,

    provideSignalDetail_data: [],
    provideSignalDetail_perPage: 10,
    provideSignalDetail_total: 0,
    provideSignalDetail_page: 1,
    provideSignalDetail_loading: false,

    copySignal_data: [],
    copySignal_perPage: 10,
    copySignal_total: 0,
    copySignal_page: 1,
    copySignal_loading: false,
}

const getters = {
    accounts_data: state => state.accounts_data,
    accounts_perPage: state => state.accounts_perPage,
    accounts_total: state => state.accounts_total,
    accounts_page: state => state.accounts_page,
    accounts_loading: state => state.accounts_loading,

    provideSignal_data: state => state.provideSignal_data,
    provideSignal_perPage: state => state.provideSignal_perPage,
    provideSignal_total: state => state.provideSignal_total,
    provideSignal_page: state => state.provideSignal_page,
    provideSignal_loading: state => state.provideSignal_loading,

    provideSignalDetail_data: state => state.provideSignalDetail_data,
    provideSignalDetail_perPage: state => state.provideSignalDetail_perPage,
    provideSignalDetail_total: state => state.provideSignalDetail_total,
    provideSignalDetail_page: state => state.provideSignalDetail_page,
    provideSignalDetail_loading: state => state.provideSignalDetail_loading,

    copySignal_data: state => state.copySignal_data,
    copySignal_perPage: state => state.copySignal_perPage,
    copySignal_total: state => state.copySignal_total,
    copySignal_page: state => state.copySignal_page,
    copySignal_loading: state => state.copySignal_loading,
}

const actions = {
    getAccountsAction(context, option) {
        context.commit('setAccountsLoadingHandler', true);
        Nprogress.start();
        axios.get(`${webServices.baseURL}/accounts?page=${option.page}&perPage=${option.perPage}`, { headers: { 'Content-Type': 'application/json' } })
            .then(response => {
                const { api_status, page, perPage, total, accounts } = response.data.response;
                if (api_status) {
                    context.commit('setAccountsHandler', { page, perPage, total, accounts });
                } else {
                    context.commit('setAccountsHandler', { page: 1, perPage: 10, total: 0, accounts: [] });
                }
            })
            .catch(error => {
                context.commit('setAccountsHandler', { page: 1, perPage: 10, total: 0, accounts: [] });
                console.log(error);
                console.log("Failed");
            })
            .finally(() => {
                Nprogress.done();
            })
    },

    getProvideSignalAction(context, option) {
        context.commit('setProvideSignalLoadingHandler', false);
        Nprogress.start();
        axios.get(`${webServices.baseURL}/providesources?page=${option.page}&perPage=${option.perPage}`, { headers: { 'Content-Type': 'application/json' } })
            .then(response => {
                const { api_status, page, perPage, total, provideSignal } = response.data.response;
                if (api_status) {
                    context.commit('setProvideSignalHandler', { page, perPage, total, provideSignal });
                } else {
                    context.commit('setProvideSignalHandler', { page: 1, perPage: 10, total: 0, provideSignal: [] });
                }
            })
            .catch(error => {
                context.commit('setProvideSignalHandler', { page: 1, perPage: 10, total: 0, provideSignal: [] });
                console.log(error);
                console.log("Failed");
            })
            .finally(() => {
                Nprogress.done();
            })
    },

    getProvideSignalDetailAction(context, option) {
        context.commit('setProvideSignalDetailLoadingHandler', false);
        Nprogress.start();
        axios.get(`${webServices.baseURL}/providesources/${option.account_number}?page=${option.page}&perPage=${option.perPage}`, { headers: { 'Content-Type': 'application/json' } })
            .then(response => {
                const { api_status, page, perPage, total, provideSignalDetail } = response.data.response;
                if (api_status) {
                    context.commit('setProvideSignalDetailHandler', { page, perPage, total, provideSignalDetail });
                } else {
                    context.commit('setProvideSignalDetailHandler', { page: 1, perPage: 10, total: 0, provideSignalDetail: [] });
                }
            })
            .catch(error => {
                context.commit('setProvideSignalDetailHandler', { page: 1, perPage: 10, total: 0, provideSignalDetail: [] });
                console.log(error);
                console.log("Failed");
            })
            .finally(() => {
                Nprogress.done();
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
        state.accounts_loading = false;
    },
    setAccountsLoadingHandler(state, loading) {
        state.accounts_loading = loading;
    },

    setProvideSignalHandler(state, payload) {
        const { page, perPage, total, provideSignal } = payload;
        state.provideSignal_page = page;
        state.provideSignal_perPage = perPage;
        state.provideSignal_total = total;
        state.provideSignal_data = provideSignal;
        state.provideSignal_loading = false;
    },
    setProvideSignalLoadingHandler(state, loading) {
        state.provideSignal_loading = loading;
    },

    setProvideSignalDetailHandler(state, payload) {
        const { page, perPage, total, provideSignalDetail } = payload;
        state.provideSignalDetail_page = page;
        state.provideSignalDetail_perPage = perPage;
        state.provideSignalDetail_total = total;
        state.provideSignalDetail_data = provideSignalDetail;
        state.provideSignalDetail_loading = false;
    },
    setProvideSignalDetailLoadingHandler(state, loading) {
        state.provideSignalDetail_loading = loading;
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}