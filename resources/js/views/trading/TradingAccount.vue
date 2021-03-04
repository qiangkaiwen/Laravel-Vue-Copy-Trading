<template>
    <div>
        <page-title-bar></page-title-bar>
        <app-section-loader :status="accounts_loading"></app-section-loader>
        <v-container fluid class="grid-list-xl pt-0 mt-n3">
            <v-row>
                <app-card :fullBlock="true" colClasses="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <v-row class="align-items-center search-wrap">
                        <v-col cols="12" md="12" lg="12" class="align-items-center pt-0">
                            <app-card customClasses="mb-0 pt-8">
                                <v-row>
                                    <v-col cols="12" md="12" lg="12" class="pb-0">
                                        <div class="d-flex ">
                                            <div class="w-75">
                                                <v-text-field class=" pt-0 pr-3" label="Search Accounts">
                                                </v-text-field>
                                            </div>
                                            <div>
                                                <v-btn color="primary" class="my-0 ml-0 mr-2" medium><i
                                                        class="material-icons">search</i>&nbsp;&nbsp;Search</v-btn>
                                            </div>
                                        </div>
                                    </v-col>
                                </v-row>
                            </app-card>
                        </v-col>
                    </v-row>
                    <v-data-table :headers="headers" :items="accounts_data" :search="search" item-key="email"
                        :server-items-length="accounts_total" :options.sync="options" :loading="accounts_loading"
                        :footer-props="{showFirstLastPage: true,}" :items-per-page-options="[5, 10, 15, 20, -1]">
                        <template slot="headerCell" slot-scope="props" :loading-text="'Loading... Please wait'">
                            <v-tooltip bottom>
                                <span slot="activator">
                                    {{ props.header.text }}
                                </span>
                                <span>
                                    {{ props.header.text }}
                                </span>
                            </v-tooltip>
                        </template>
                        <template v-slot:item="props">
                            <tr>
                                <td>{{ props.index + 1 }}</td>
                                <td>{{ props.item.account_number }}</td>
                                <td>{{ props.item.broker }}</td>
                                <td>
                                    <v-badge :value=false :class="props.item.statusColor">{{ props.item.status }}
                                    </v-badge>
                                </td>
                                <td>{{ getDateFormat(props.item.created_at) }}</td>
                                <td>
                                    <template v-if="props.item.status == 'PROVIDE'">
                                        <router-link
                                            :to="{ name: 'signal-detail', params: { account_number: props.item.account_number, broker: props.item.broker } }">
                                            <v-tooltip bottom>
                                                <template v-slot:activator="{ on }">
                                                    <v-btn v-on="on" icon color="primary">
                                                        <v-icon class="zmdi zmdi-eye"></v-icon>
                                                    </v-btn>
                                                </template>
                                                <span>View This account</span>
                                            </v-tooltip>
                                        </router-link>
                                        <v-tooltip bottom>
                                            <template v-slot:activator="{ on }">
                                                <v-btn v-on="on" icon color="success"
                                                    @click="changeSetting(props.item.account_number, props.item.broker, 'provide')">
                                                    <v-icon class="zmdi zmdi-settings"></v-icon>
                                                </v-btn>
                                            </template>
                                            <span>Change Provide Settings</span>
                                        </v-tooltip>
                                    </template>

                                    <template v-else>
                                        <router-link
                                            :to="{ name: 'copying-signal', hash: '#' + props.item.account_number }">
                                            <v-tooltip bottom>
                                                <template v-slot:activator="{ on }">
                                                    <v-btn v-on="on" icon color="primary">
                                                        <v-icon class="zmdi zmdi-eye"></v-icon>
                                                    </v-btn>
                                                </template>
                                                <span>View This account</span>
                                            </v-tooltip>
                                        </router-link>
                                        <v-tooltip bottom>
                                            <template v-slot:activator="{ on }">
                                                <v-btn v-on="on" icon color="success"
                                                    @click="changeSetting(props.item.account_number, props.item.broker, 'copy')">
                                                    <v-icon class="zmdi zmdi-settings"></v-icon>
                                                </v-btn>
                                            </template>
                                            <span>Change Copy Settings</span>
                                        </v-tooltip>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </v-data-table>
                </app-card>
                <copy-setting-dialog :settingType="settingType" :dismiss="dismissModal" :settingModal="settingModal"
                    :form.sync="form" :formstep.sync="formstep" :isLoadingSettings="isLoadingSettings"
                    :isFormSubmitting="isFormSubmitting" :prevStep="prevStep" :checkNextisDisabled="checkNextisDisabled"
                    :saveSettings="saveSettings" :nextStep="nextStep">
                </copy-setting-dialog>
            </v-row>
        </v-container>
    </div>
</template>

<script>
    import { mapGetters, mapActions } from "vuex";
    import dateformat from 'dateformat';
    import axios from "axios";
    import webServices from "WebServices";
    import Vue from "vue";
    import Nprogress from 'nprogress';

    export default {
        data() {
            return {
                search: "",
                headers: [
                    {
                        text: "#",
                        align: "left",
                        sortable: false,
                        value: 'id'
                    },
                    { text: "Account Number", value: "account_number", sortable: false },
                    { text: "Broker", value: "account_number", sortable: false },
                    { text: "Status", value: "online_status", sortable: false },
                    { text: "Created At", value: "created_at", sortable: false },
                    { text: "", value: "actions", sortable: false },
                ],
                options: {},
                isLoadingSettings: false,
                selected_account: null,
                formstep: 1,
                settingType: 'copy',
                settingModal: false,
                isFormSubmitting: false,
                form: null,
            };
        },
        mounted() { },
        methods: {
            ...mapActions([
                'getAccountsAction'
            ]),
            ...{
                getDateFormat(date) {
                    if (!date) return '';
                    return dateformat(new Date(date), "mmm, dd yyyy HH:MM")
                },
                getDateFormatWithMS(date) {
                    if (!date) return '';
                    date = parseInt(date);
                    return dateformat(date, "mmm, dd yyyy HH:MM")
                },
                dismissModal() {
                    this.selected_account = null;
                    this.settingModal = false;
                    this.form = null;
                    this.isFormSubmitting = false;
                },
                saveSettings() {
                    if (this.isFormSubmitting) return;
                    this.isFormSubmitting = true;
                    Nprogress.start();
                    if (this.settingType == 'copy') {
                        axios.post(`${webServices.baseURL}/savecopysetting`, { ...this.form, ...this.selected_account })
                            .then(({ data }) => {
                                Vue.notify({
                                    group: 'signals',
                                    type: 'success',
                                    text: 'Setting saved!'
                                });
                                this.selected_account = null;
                                this.settingModal = false;
                                this.form = null;
                            })
                            .catch(() => {
                                Vue.notify({
                                    group: 'signals',
                                    type: 'error',
                                    text: 'Can\'t save setting'
                                });
                            })
                            .finally(() => {
                                this.isFormSubmitting = false;
                                Nprogress.done();
                            });
                    }
                    else {
                        axios.post(`${webServices.baseURL}/saveprovidesetting`, { ...this.form, ...this.selected_account })
                            .then(({ data }) => {
                                Vue.notify({
                                    group: 'signals',
                                    type: 'success',
                                    text: 'Setting saved!'
                                });
                                this.selected_account = null;
                                this.settingModal = false;
                                this.form = null;
                            })
                            .catch(() => {
                                Vue.notify({
                                    group: 'signals',
                                    type: 'error',
                                    text: 'Can\'t save setting'
                                });
                            })
                            .finally(() => {
                                this.isFormSubmitting = false;
                                Nprogress.done();
                            });
                    }
                },
                nextStep() {
                    this.formstep++;
                },
                prevStep() {
                    this.formstep--;
                },
                changeSetting(account_number, broker, settingType) {
                    this.settingType = settingType;
                    this.settingModal = true;
                    this.isLoadingSettings = true;
                    this.formstep = 1;
                    this.selected_account = {
                        account_number,
                        broker
                    };
                    Nprogress.start();
                    if (settingType == 'copy') {
                        axios.post(`${webServices.baseURL}/copysetting`, this.selected_account)
                            .then(({ data }) => {
                                const { response } = data;
                                this.form = response.setting;
                            })
                            .catch(() => {
                                this.form = null;
                            })
                            .finally(() => {
                                this.isLoadingSettings = false;
                                Nprogress.done();
                            });
                    }
                    else {
                        axios.post(`${webServices.baseURL}/providesetting`, this.selected_account)
                            .then(({ data }) => {
                                const { response } = data;
                                this.form = response.setting;
                            })
                            .catch(() => {
                                this.form = null;
                            })
                            .finally(() => {
                                this.isLoadingSettings = false;
                                Nprogress.done();
                            });
                    }
                }
            }
        },
        computed: {
            ...mapGetters([
                "accounts_data",
                "accounts_perPage",
                "accounts_total",
                "accounts_page",
                "accounts_loading"
            ]),
            ... {
                checkNextisDisabled() {
                    switch (this.formstep) {
                        case 6:
                            if (!this.form.applyOnOffTime)
                                return false;
                            return (!this.form.onTime || !this.form.offTime);
                        default:
                            return false;
                    }
                },
            }
        },

        watch: {
            options: function (options) {
                this.getAccountsAction({ page: options.page, perPage: options.itemsPerPage });
            }
        }
    };
</script>