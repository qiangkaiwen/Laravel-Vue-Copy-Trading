/**
 * Auth Module
 */
import Vue from 'vue';
import webServices from 'WebServices';
import axios from "axios";
import Nprogress from 'nprogress';
import router from '../../../router';

const state = {
    user: JSON.parse(localStorage.getItem('user')),
    isUserSigninWithAuth0: Boolean(localStorage.getItem('isUserSigninWithAuth0'))
}

// getters
const getters = {
    getUser: state => {
        return state.user;
    },
    isUserSigninWithAuth0: state => {
        return state.isUserSigninWithAuth0;
    }
}

// actions
const actions = {
    signupUserWithLaravelPassport(context, payload) {
        context.commit('signUpUser');
        axios.post(`${webServices.baseURL}/auth/signup`, payload.userDetail, { headers: { 'Content-Type': 'application/json' } })
            .then(response => {
                if (response.data.response.api_status) {
                    setTimeout(() => {
                        context.commit('signUpUserSuccess', payload);
                    }, 500);
                } else {
                    context.commit('signUpUserFailure', response.data.response);
                }
            })
            .catch(error => {
                let payload = {
                    message: 'Signup failed.'
                }
                if (error.response) {
                    const { response } = error.response.data;
                    payload.message = response.message;
                }
                context.commit('signUpUserFailure', payload);
            })
            .finally(() => {
                Nprogress.done();
            })
    },
    signInWithLaravelPassport(context, payload) {
        const { user } = payload;
        context.commit('loginUser');
        axios.post(`${webServices.baseURL}/auth/login`, user, { headers: { 'Content-Type': 'application/json' } })
            .then(response => {
                const { api_status, access_token, name, email, roles, avatar } = response.data.response;
                if (api_status) {
                    localStorage.setItem("access_token", access_token);
                    setTimeout(() => {
                        context.commit('loginUserSuccess', { name, email, roles, avatar });
                    }, 500);
                } else {
                    context.commit('loginUserFailure', response.data.response);
                }
            })
            .catch(error => {
                let payload = {
                    message: 'Login failed.'
                }
                if (error.response) {
                    const { response } = error.response.data;
                    payload.message = response.message;
                }
                context.commit('loginUserFailure', payload);
            })
            .finally(() => {
                Nprogress.done();
            })
    },
    signOutWithLaravelPassport(context) {
        axios.post(`${webServices.baseURL}/auth/logout`, {}, { headers: { 'Content-Type': 'application/json' } })
            .finally(() => {
                context.commit('logoutUser');
            })
    },
    signInUserWithAuth0(context, payload) {
        context.commit('signInUserWithAuth0Success', payload);
    },
    signOutUserFromAuth0(context) {
        context.commit('signOutUserFromAuth0Success');
    },
    changeAvatarAction(context, avatar) {
        context.commit('changeAvatar', avatar)
    }
}

// mutations
const mutations = {
    loginUser() {
        Nprogress.start();
    },
    loginUserSuccess(state, user) {
        state.user = user;
        localStorage.setItem('user', JSON.stringify(user));
        state.isUserSigninWithAuth0 = false;
        if (user.roles.findIndex(role => role == 'ROLE_ADMIN') !== -1)
            router.push("/users-list");
        else if (user.roles.findIndex(role => role == 'ROLE_MANAGER') !== -1)
            router.push("/users-list");
        else router.push("/trading-accounts");

        setTimeout(function () {
            Vue.notify({
                group: 'loggedIn',
                type: 'success',
                text: 'User Logged In Success!'
            });
        }, 1500);
    },
    loginUserFailure(state, error) {
        Nprogress.done();
        Vue.notify({
            group: 'loggedIn',
            type: 'error',
            text: error.message
        });
    },
    logoutUser(state) {
        state.user = null
        localStorage.removeItem('user');
        localStorage.removeItem('access_token');
        router.push("/session/login");
    },
    signUpUser() {
        Nprogress.start();
    },
    signUpUserSuccess(state, user) {
        // state.user = localStorage.setItem('user', user);
        router.push("/session/login");
        Vue.notify({
            group: 'loggedIn',
            type: 'success',
            text: 'Account Created!'
        });
    },
    signUpUserFailure(state, error) {
        Nprogress.done();
        Vue.notify({
            group: 'loggedIn',
            type: 'error',
            text: error.message
        });
    },
    signInUserWithAuth0Success(state, user) {
        state.user = user;
        localStorage.setItem('user', JSON.stringify(user));
        state.isUserSigninWithAuth0 = true;
    },
    signOutUserFromAuth0Success(state) {
        state.user = null
        localStorage.removeItem('user')
    },
    changeAvatar(state, avatar) {
        if (state.user) {
            state.user.avatar = avatar;
            localStorage.setItem('user', JSON.stringify(state.user));
        }
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}
