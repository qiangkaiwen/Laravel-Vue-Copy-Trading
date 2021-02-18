<template>
    <div>
        <page-title-bar></page-title-bar>
        <app-section-loader :status="provideSignal_loading"></app-section-loader>
        <v-container fluid class="grid-list-xl pt-0 mt-n3">
            <v-row>
                <app-card :fullBlock="true" colClasses="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <v-data-table :key="tableProvideKey" :headers="headers" :items="provideSignal_data" :search="search"
                        item-key="email" :server-items-length="provideSignal_total" :options.sync="options"
                        :loading="provideSignal_loading" :footer-props="{showFirstLastPage: true,}"
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
                tableProvideKey: 0,
            };
        },
        mounted() {
        },
        methods: {
            ...mapActions([
                'getProvideSignalAction'
            ]),
            ...{
                getDateFormat(date) {
                    return dateformat(new Date(date), "mmm, dd yyyy HH:MM")
                },
                gotoDetail(account_number, broker) {
                    this.$router.push({ path: `signal-detail/${account_number}/${broker}` });
                },
                deleteSource(id) {
                    this.delete_id = id;
                    this.$refs.deleteConfirmationDialog.openDialog();
                },
                deleteSourceConfirm() {
                    axios.delete(`${webServices.baseURL}/providesource/${this.delete_id}`).then(() => {
                        this.$refs.deleteConfirmationDialog.close();
                        this.tableProvideKey++;
                    })
                },
                provideSource(account_number) {
                    console.log(account_number);
                }
            }
        },
        computed: {
            ...mapGetters([
                "provideSignal_data",
                "provideSignal_perPage",
                "provideSignal_total",
                "provideSignal_page",
                "provideSignal_loading"
            ]),
        },

        watch: {
            options: function (options) {
                this.getProvideSignalAction({
                    page: options.page,
                    perPage: options.itemsPerPage
                });
            }
        }
    };
</script>