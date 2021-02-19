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
                                            <div class="w-50">
                                                <v-text-field class=" pt-0 pr-3" label="Search Signal">
                                                </v-text-field>
                                            </div>
                                            <div>
                                                <v-btn color="primary" class="my-0 ml-0 mr-2" medium>
                                                    <i class="material-icons">search</i>&nbsp;&nbsp;Search
                                                </v-btn>
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
                        :loading="copySignal_loading" :footer-props="{showFirstLastPage: true,}"
                        :items-per-page-options="[5, 10, 15, 20, -1]">
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
                                <td>{{ props.item.signal_number }}</td>
                                <td>{{ getDateFormat(props.item.openTime) }}</td>
                                <td>{{ props.item.copier_number }}</td>
                                <td>
                                    <v-btn text icon color="primary"
                                        @click="gotoDetail(props.item.account_number, props.item.broker)">
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
    </div>
</template>

<script>
    import { mapGetters, mapActions } from "vuex";
    import dateformat from "dateformat";
    import axios from "axios";
    import webServices from "WebServices";

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
                    { text: "Broker", sortable: false },
                    { text: "Source Account", sortable: false },
                    { text: "Number of Signals", sortable: false },
                    { text: "Since", sortable: false },
                    { text: "Number Of Copiers", sortable: false },
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
        mounted() {
        },
        methods: {
            ...mapActions([
                'getCopySignalAction'
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
                gotoAllProvide() {
                    this.$router.push({ name: `available-signal` });
                },
                deleteSource(id) {
                    this.delete_id = id;
                    this.$refs.deleteConfirmationDialog.openDialog();
                },
                deleteSourceConfirm() {
                    axios.delete(`${webServices.baseURL}/copysource/${this.delete_id}`).then(() => {
                        this.$refs.deleteConfirmationDialog.close();
                        this.tableCopyKey++;
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
        },

        watch: {
            options: function (options) {
                this.getCopySignalAction({
                    page: options.page,
                    perPage: options.itemsPerPage
                });
            }
        }
    };
</script>