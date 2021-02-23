<template>
    <div>
        <page-title-bar></page-title-bar>
        <app-section-loader :status="copySignal_loading"></app-section-loader>
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
                                                <v-text-field class=" pt-0 pr-3" label="Search Signal" v-model="search">
                                                </v-text-field>
                                            </div>
                                            <div>
                                                <!-- <v-btn color="primary" class="my-0 ml-0 mr-2" medium
                                                    @click="searchfinal = search">
                                                    <i class="material-icons">search</i>&nbsp;&nbsp;Search
                                                </v-btn> -->
                                                <v-btn color="primary m-0" medium @click="gotoAllProvide">
                                                    <i class="material-icons">add</i>&nbsp;&nbsp;Add
                                                </v-btn>
                                            </div>
                                        </div>
                                    </v-col>
                                </v-row>
                            </app-card>
                        </v-col>
                    </v-row>
                    <v-data-table :key="tableCopyKey" :headers="headers" :items="copySignal_data" :search="search"
                        item-key="email" :server-items-length="copySignal_total" :options.sync="options"
                        :loading="copySignal_loading"
                        :footer-props="{ showFirstLastPage: true, itemsPerPageOptions: [5, 10, 15, 20] }">
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
                                <td>{{ props.item.broker }}</td>
                                <td>{{ props.item.account_number }}</td>
                                <td>{{ props.item.provider_name }}</td>
                                <td>{{ props.item.src_account_number }}</td>
                                <td>{{ getDateFormatWithMS(props.item.created_at) }}</td>
                                <td>{{ props.item.signal_number }}</td>
                                <td>
                                    <v-btn text icon color="primary"
                                        @click="gotoDetail(props.item.src_account_number, props.item.src_broker)">
                                        <v-icon class="zmdi zmdi-eye"></v-icon>
                                    </v-btn>
                                    <v-btn text icon color="error" @click="deleteSource(props.item.id)">
                                        <v-icon class="zmdi zmdi-delete"></v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                        </template>
                    </v-data-table>
                </app-card>
            </v-row>
        </v-container>
        <delete-confirmation-dialog ref="deleteConfirmationDialog" heading="Are You Sure You Want To Delete?"
            message="Are you sure you want to delete this Source permanently?" @onConfirm="deleteSourceConfirm">
        </delete-confirmation-dialog>
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
                hash: "",
                headers: [
                    {
                        text: "#",
                        align: "left",
                        sortable: false,
                    },
                    { text: "Broker", sortable: false },
                    { text: "Copied Account", sortable: false },
                    { text: "Provider", sortable: false },
                    { text: "Source Account", sortable: false },
                    { text: "Copied Since", sortable: false },
                    { text: "Number Of Copied Signals", sortable: false },
                    { text: "", sortable: false },
                ],
                options: {},
                delete_id: null,
                tableCopyKey: 0,
                form: {
                    valid: true,
                    account: null,
                    accountRules: [
                        v => !!v || "Please choose an account.",
                    ],
                },
                accounts: []
            };
        },
        beforeMount() {
            let hash = this.$route.hash;
            if (hash) {
                this.hash = hash.substring(1);
            }
        },
        mounted() {
        },
        methods: {
            ...mapActions([
                'getCopySignalAction'
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
                gotoAllProvide() {
                    this.$router.push({ name: `available-signal` });
                },
                deleteSource(id) {
                    this.delete_id = id;
                    this.$refs.deleteConfirmationDialog.openDialog();
                },
                deleteSourceConfirm() {
                    axios.delete(`${webServices.baseURL}/copysource/${this.delete_id}`)
                        .then(({ data }) => {
                            const { api_status, message } = data.response;
                            this.tableCopyKey++;
                            if (api_status) {
                                Vue.notify({
                                    group: 'signals',
                                    type: 'success',
                                    text: 'Delete success!'
                                });
                            } else {
                                Vue.notify({
                                    group: 'signals',
                                    type: 'error',
                                    text: message
                                });
                            }
                        })
                        .catch((error) => {
                            let message = 'Delete copying failed.';
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
                            this.$refs.deleteConfirmationDialog.close();
                        })
                },
            }
        },
        computed: {
            ...mapGetters([
                "copySignal_data",
                "copySignal_perPage",
                "copySignal_total",
                "copySignal_page",
                "copySignal_loading"
            ]),
            ...{
                serverOptions: function () {
                    return {
                        ...this.options, ...{
                            search: this.search,
                            hash: this.hash,
                        }
                    };
                }
            }
        },

        watch: {
            serverOptions: function (serverOptions) {
                this.getCopySignalAction({
                    page: serverOptions.page,
                    perPage: serverOptions.itemsPerPage,
                    hash: serverOptions.hash
                });
            },
        }
    };
</script>