<template>
    <div>
        <page-title-bar></page-title-bar>
        <app-section-loader :status="provideSignalDetail_loading"></app-section-loader>
        <v-container fluid class="grid-list-xl pt-0 mt-n3">
            <v-row>
                <app-card :fullBlock="true" colClasses="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <v-data-table :headers="headers" :items="provideSignalDetail_data" :search="search" item-key="email"
                        :server-items-length="provideSignalDetail_total" :options.sync="options"
                        :loading="provideSignalDetail_loading" :footer-props="{showFirstLastPage: true,}"
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
                                <td>{{ props.item.account_number }}</td>
                                <td>{{ props.item.symbol }}</td>
                                <td>{{ props.item.signal_number }}</td>
                                <td>{{ getDateFormat(props.item.openTime) }}</td>
                                <td>25</td>
                                <td>10%</td>
                                <td></td>
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
                    { text: "Source ID", sortable: false },
                    { text: "Symbol", sortable: false },
                    { text: "Number of Signals", sortable: false },
                    { text: "Start From", sortable: false },
                    { text: "Number Of Followers", sortable: false },
                    { text: "Gross Profit", sortable: false },
                    { text: "", sortable: false },
                ],
                options: {}
            };
        },
        mounted() {
        },
        methods: {
            ...mapActions([
                'getProvideSignalDetailAction'
            ]),
            ...{
                getDateFormat(date) {
                    return dateformat(new Date(date), "mmm, dd yyyy HH:MM")
                }
            }
        },
        computed: {
            ...mapGetters([
                "provideSignalDetail_data",
                "provideSignalDetail_perPage",
                "provideSignalDetail_total",
                "provideSignalDetail_page",
                "provideSignalDetail_loading"
            ]),
        },

        watch: {
            options: function (options) {
                this.getProvideSignalDetailAction({
                    page: options.page,
                    perPage: options.itemsPerPage,
                    account_number: this.$route.params.account_number,
                });
            }
        }
    };
</script>