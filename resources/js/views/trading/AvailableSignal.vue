<template>
    <div>
        <page-title-bar></page-title-bar>
        <app-section-loader :status="availableSignal_loading"></app-section-loader>
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
                                                <v-text-field class=" pt-0 pr-3" label="Search Signal">
                                                </v-text-field>
                                            </div>
                                            <div>
                                                <v-btn color="primary" class="my-0 ml-0 mr-2" medium>
                                                    <i class="material-icons">search</i>&nbsp;&nbsp;Search
                                                </v-btn>
                                            </div>
                                        </div>
                                    </v-col>
                                </v-row>
                            </app-card>
                        </v-col>
                    </v-row>
                    <v-data-table :key="tableProvideKey" :headers="headers" :items="availableSignal_data"
                        :search="search" item-key="email" :server-items-length="availableSignal_total"
                        :options.sync="options" :loading="availableSignal_loading"
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
                                <td>{{ props.item.provider }}</td>
                                <td>{{ props.item.broker }}</td>
                                <td>{{ props.item.account_number }}</td>
                                <td>{{ props.item.signal_number }}</td>
                                <td>{{ getDateFormat(props.item.openTime) }}</td>
                                <td>{{ props.item.copier_number }}</td>
                                <td>
                                    <v-btn text icon color="primary"
                                        @click="gotoDetail(props.item.account_number, props.item.broker)">
                                        <v-icon class="zmdi zmdi-eye"></v-icon>
                                    </v-btn>
                                    <v-btn text icon color="success"
                                        @click="tryCopyAccount(props.item.broker, props.item.account_number)">
                                        <v-icon class="zmdi zmdi-playlist-plus"></v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                        </template>
                    </v-data-table>
                </app-card>
            </v-row>
        </v-container>

        <template>
            <v-dialog v-model="copyModal" max-width="600">
                <v-card class="pa-6">
                    <v-form v-model="form.valid" ref="form" lazy-validation>
                        <h2>Please select source account nummber to available signal source.</h2>
                        <v-select class="mb-3" hide-details v-bind:items="accounts" v-model="form.account"
                            :rules="form.accountRules" label="Select Account" single-line menu-props="auto" required>
                        </v-select>
                        </v-row>
                        <v-btn @click="copySource" :disabled="!form.valid" color="success" class="mr-3">
                            Copy
                        </v-btn>
                        <v-btn color="primary" @click="copyModal = false" class="px-4">Cancel</v-btn>
                    </v-form>
                </v-card>
            </v-dialog>
        </template>
        <confirmation-dialog ref="copyConfirmationDialog" heading="Are You Sure You Want To Copy?"
            message="Are you sure you want to copy this Account?" @onConfirm="copyConfirm" confirmColor="success">
        </confirmation-dialog>
    </div>
</template>

<script>
    import { mapGetters, mapActions } from "vuex";
    import dateformat from "dateformat";
    import axios from "axios";
    import webServices from "WebServices";
    import Vue from "vue";

    export default {
        data() {
            return {
                search: "",
                headers: [
                    {
                        text: "#",
                        align: "left",
                        sortable: false,
                    },
                    { text: "Provider", sortable: false },
                    { text: "Broker", sortable: false },
                    { text: "Source Account", sortable: false },
                    { text: "Number of Signals", sortable: false },
                    { text: "Since", sortable: false },
                    { text: "Number Of Copiers", sortable: false },
                    { text: "", sortable: false },
                ],
                options: {},
                delete_id: null,
                tableProvideKey: 0,
                copyModal: false,
                form: {
                    valid: true,
                    account: null,
                    accountRules: [
                        v => !!v || "Please choose an account.",
                    ],
                },
                source_account: null,
                accounts: []
            };
        },
        mounted() {
            axios.get(`${webServices.baseURL}/accounts-for-copy`, { headers: { 'Content-Type': 'application/json' } })
                .then(({ data }) => {
                    const { api_status, accounts } = data.response;
                    if (api_status) {
                        this.accounts = accounts.map(account => {
                            return {
                                text: account.account_number + " (" + account.broker + ")",
                                value: account,
                            }
                        })
                    }
                })
                .catch(() => {
                    Vue.notify({
                        group: 'signals',
                        type: 'error',
                        text: 'Loading accounts failed!'
                    });
                })
        },
        methods: {
            ...mapActions([
                'getAvailableSignalAction'
            ]),
            ...{
                getDateFormat(date) {
                    if (!date) return '';
                    let ndate = parseInt(date);
                    let dateObj = new Date(ndate);
                    if (isNaN(dateObj.getTime())) {
                        console.log(date, ndate);
                        return '';
                    }
                    return dateformat(dateObj, "mmm, dd yyyy HH:MM")
                },
                gotoDetail(account_number, broker) {
                    this.$router.push({ path: `signal-detail/${account_number}/${broker}` });
                },
                tryCopyAccount(broker, account_number) {
                    this.copyModal = true;
                    this.source_account = { broker, account_number };
                },
                copySource() {
                    if (!this.form.account) return;
                    this.copyModal = false;
                    this.$refs.copyConfirmationDialog.openDialog();
                },
                copyConfirm() {
                    axios.post(`${webServices.baseURL}/copysources`,
                        { source_account: this.source_account, account: this.form.account },
                        { headers: { 'Content-Type': 'application/json' } })
                        .then(({ data }) => {
                            const { api_status, message } = data.response;

                            if (api_status) {
                                this.tableProvideKey++;
                                Vue.notify({
                                    group: 'signals',
                                    type: 'success',
                                    text: 'Copying signal success!'
                                });
                            }
                            else {
                                Vue.notify({
                                    group: 'signals',
                                    type: 'error',
                                    text: message
                                });
                            }
                        })
                        .catch((error) => {
                            let message = 'Copying signal failed.';
                            if (error.response) {
                                const { response } = error.response.data;
                                message = response.message;
                            }
                            Vue.notify({
                                group: 'signals',
                                type: 'error',
                                text: message
                            });
                        })
                        .finally(() => {
                            this.form.account = null;
                            this.$refs.copyConfirmationDialog.close();
                        });
                }
            }
        },
        computed: {
            ...mapGetters([
                "availableSignal_data",
                "availableSignal_perPage",
                "availableSignal_total",
                "availableSignal_page",
                "availableSignal_loading"
            ]),
        },

        watch: {
            options: function (options) {
                this.getAvailableSignalAction({
                    page: options.page,
                    perPage: options.itemsPerPage
                });
            }
        }
    };
</script>