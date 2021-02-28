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
                                <td>{{ getDateFormatWithMS(props.item.openTime) }}</td>
                                <td>{{ props.item.copier_number }}</td>
                                <td>
                                    <v-tooltip bottom>
                                        <template v-slot:activator="{ on }">
                                            <v-btn v-on="on" text icon color="primary"
                                                @click="gotoDetail(props.item.account_number, props.item.broker)">
                                                <v-icon class="zmdi zmdi-eye"></v-icon>
                                            </v-btn>
                                        </template>
                                        <span>View Source Detail</span>
                                    </v-tooltip>
                                    <v-tooltip bottom>
                                        <template v-slot:activator="{ on }">
                                            <v-btn v-on="on" text icon color="success"
                                                @click="tryCopyAccount(props.item.broker, props.item.account_number)">
                                                <v-icon class="zmdi zmdi-copy"></v-icon>
                                            </v-btn>
                                        </template>
                                        <span>Copy This Source</span>
                                    </v-tooltip>
                                </td>
                            </tr>
                        </template>
                    </v-data-table>
                </app-card>
            </v-row>
        </v-container>

        <template>
            <v-dialog v-model="copyModal" max-width="800">
                <v-form ref="form" lazy-validation>
                    <template>
                        <v-card class="mx-auto px-6" v-if="source_account">
                            <v-card-title class="title font-weight-regular justify-space-between">
                                <span>Source from {{ source_account.account_number }} / {{ source_account.broker }} will
                                    be copied to:</span>
                                <v-avatar color="primary lighten-2" class="subheading white--text" size="40">
                                    <v-icon class="zmdi zmdi-copy"></v-icon>
                                </v-avatar>
                            </v-card-title>
                            <v-divider></v-divider>
                            <v-row class="mt-5 mb-3">
                                <v-col cols="12" sm="12" md="5" class="py-0">
                                    <h4>
                                        My Available Accounts
                                    </h4>
                                    <v-list style="height: 300px; max-height: 300px"
                                        class="mt-2 overflow-y-auto border">
                                        <v-list-item-group v-model="availableaccounts_selected" multiple>
                                            <v-list-item v-for="(account, index) in availableaccounts" :key="index">
                                                <template v-slot:default="{ active, }">
                                                    <v-list-item-action>
                                                        <v-checkbox :input-value="active" color="primary">
                                                        </v-checkbox>
                                                    </v-list-item-action>
                                                    <v-list-item-content>
                                                        <v-list-item-title>{{ account.account_number }}
                                                        </v-list-item-title>
                                                        <v-list-item-subtitle>{{ account.broker }}
                                                        </v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </template>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                </v-col>
                                <v-col cols="12" sm="12" md="2" class="d-flex align-center py-0">
                                    <div>
                                        <v-btn class="mt-3" fab dark small color="pink" @click="addToCopy">
                                            <v-icon>mdi-chevron-right</v-icon>
                                        </v-btn>
                                        <v-btn class="mt-3" fab dark small color="pink" @click="addToCopyAll">
                                            <v-icon>mdi-chevron-double-right</v-icon>
                                        </v-btn>
                                        <v-btn class="mt-3" fab dark small color="pink" @click="removeFromCopy">
                                            <v-icon>mdi-chevron-left</v-icon>
                                        </v-btn>
                                        <v-btn class="mt-3" fab dark small color="pink" @click="removeFromCopyAll">
                                            <v-icon>mdi-chevron-double-left</v-icon>
                                        </v-btn>
                                    </div>
                                </v-col>
                                <v-col cols="12" sm="12" md="5" class="py-0">
                                    <h4>
                                        Selected Accounts
                                    </h4>
                                    <v-list style="height: 300px; max-height: 300px"
                                        class="mt-2 overflow-y-auto border">
                                        <v-list-item-group v-model="copyingaccounts_selected" multiple>
                                            <v-list-item v-for="(account, index) in copyingaccounts" :key="index">
                                                <template v-slot:default="{ active, }">
                                                    <v-list-item-action>
                                                        <v-checkbox :input-value="active" color="primary">
                                                        </v-checkbox>
                                                    </v-list-item-action>
                                                    <v-list-item-content>
                                                        <v-list-item-title>{{ account.account_number }}
                                                        </v-list-item-title>
                                                        <v-list-item-subtitle>{{ account.broker }}
                                                        </v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </template>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                </v-col>
                            </v-row>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn :disabled="isFormSubmitting" color="primary" depressed @click="copySource">
                                    Submit
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </template>
                </v-form>
            </v-dialog>
        </template>
    </div>
</template>

<script>
    import { mapGetters, mapActions } from "vuex";
    import dateformat from "dateformat";
    import axios from "axios";
    import webServices from "WebServices";
    import Vue from "vue";
    import Nprogress from 'nprogress';

    export default {
        data() {
            return {
                onTimeDialog: false,
                offTimeDialog: false,
                availableaccounts: [],
                copyingaccounts: [],
                availableaccounts_selected: [],
                copyingaccounts_selected: [],
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
                source_account: null,
                accounts: [],
                isFormSubmitting: false,
            };
        },
        mounted() {
            axios.get(`${webServices.baseURL}/accounts-for-copy`, { headers: { 'Content-Type': 'application/json' } })
                .then(({ data }) => {
                    const { api_status, accounts } = data.response;
                    if (api_status) {
                        this.accounts = accounts;
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
                    return dateformat(new Date(date), "mmm, dd yyyy HH:MM")
                },
                getDateFormatWithMS(date) {
                    if (!date) return '';
                    date = parseInt(date);
                    return dateformat(date, "mmm, dd yyyy HH:MM")
                },
                gotoDetail(account_number, broker) {
                    this.$router.push({ path: `signal-detail/${account_number}/${broker}` });
                },
                tryCopyAccount(broker, account_number) {
                    this.copyModal = true;

                    this.availableaccounts = [...this.accounts];
                    this.availableaccounts_selected = [];

                    this.copyingaccounts = [];
                    this.copyingaccounts_selected = [];

                    this.source_account = { broker, account_number };

                },
                addToCopy() {
                    if (this.availableaccounts_selected.length == 0) return;
                    this.availableaccounts_selected.map(selected => {
                        this.copyingaccounts.push(this.availableaccounts[selected]);
                    });

                    const newvals = this.availableaccounts.filter((account, index) => {
                        return this.availableaccounts_selected
                            .find(selected => selected == index) === undefined;
                    });
                    this.availableaccounts_selected = [];
                    this.availableaccounts = newvals;
                    this.copyingaccounts_selected = [];
                },
                addToCopyAll() {
                    this.availableaccounts = [];
                    this.availableaccounts_selected = [];
                    this.copyingaccounts = [...this.accounts];
                    this.copyingaccounts_selected = [];
                },
                removeFromCopy() {
                    if (this.copyingaccounts_selected.length == 0) return;
                    this.copyingaccounts_selected.map(selected => {
                        this.availableaccounts.push(this.copyingaccounts[selected]);
                    });

                    const newvals = this.copyingaccounts.filter((account, index) => {
                        return this.copyingaccounts_selected
                            .find(selected => selected == index) === undefined;
                    });
                    this.copyingaccounts_selected = [];
                    this.copyingaccounts = newvals;
                    this.availableaccounts_selected = [];
                },
                removeFromCopyAll() {
                    this.availableaccounts = [...this.accounts];
                    this.availableaccounts_selected = [];
                    this.copyingaccounts = [];
                    this.copyingaccounts_selected = [];
                },

                copySource() {
                    this.isFormSubmitting = true;
                    Nprogress.start();

                    axios.post(`${webServices.baseURL}/copysources`,
                        {
                            source_account: this.source_account,
                            accounts: this.copyingaccounts,
                        },
                        { headers: { 'Content-Type': 'application/json' } })
                        .then(({ data }) => {
                            const { api_status, message } = data.response;

                            if (api_status) {
                                this.tableProvideKey++;
                                Vue.notify({
                                    group: 'signals',
                                    type: 'success',
                                    text: message
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
                            Vue.notify({
                                group: 'signals',
                                type: 'error',
                                text: message
                            });
                        })
                        .finally(() => {
                            this.copyModal = false;
                            this.copied_account = [];
                            this.isFormSubmitting = false;
                            Nprogress.done();
                        });
                },
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
            ...{
            }
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