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

    signalDetail_data: [],
    signalDetail_perPage: 10,
    signalDetail_total: 0,
    signalDetail_page: 1,
    signalDetail_information: {},
    signalDetail_loading: false,

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

    signalDetail_data: state => state.signalDetail_data,
    signalDetail_perPage: state => state.signalDetail_perPage,
    signalDetail_total: state => state.signalDetail_total,
    signalDetail_page: state => state.signalDetail_page,
    signalDetail_loading: state => state.signalDetail_loading,
    signalDetail_information: state => state.signalDetail_information,

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

    signalDetailAction(context, option) {
        context.commit('setSignalDetailLoadingHandler', false);
        Nprogress.start();
        axios.post(`${webServices.baseURL}/signaldetail`, option, { headers: { 'Content-Type': 'application/json' } })
            .then(response => {
                const { api_status, page, perPage, total, signalDetail, information } = response.data.response;
                if (api_status) {
                    context.commit('setSignalDetailHandler', { page, perPage, total, signalDetail, information });
                } else {
                    context.commit('setSignalDetailHandler', { page: 1, perPage: 10, total: 0, signalDetail: [], information: {} });
                }
            })
            .catch(error => {
                context.commit('setSignalDetailHandler', { page: 1, perPage: 10, total: 0, signalDetail: [], information: {} });
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

    setSignalDetailHandler(state, payload) {
        const { page, perPage, total, signalDetail, information } = payload;
        state.signalDetail_page = page;
        state.signalDetail_perPage = perPage;
        state.signalDetail_total = total;
        state.signalDetail_data = signalDetail;
        state.signalDetail_information = information;
        state.signalDetail_loading = false;
    },
    setSignalDetailLoadingHandler(state, loading) {
        state.signalDetail_loading = loading;
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}