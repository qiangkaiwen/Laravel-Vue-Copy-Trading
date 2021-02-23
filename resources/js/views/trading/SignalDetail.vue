<template>
    <div>
        <page-title-bar></page-title-bar>
        <app-section-loader :status="signalDetail_loading"></app-section-loader>
        <v-container fluid class="grid-list-xl pt-0 mt-n3">
            <v-row>
                <app-card :fullBlock="true" colClasses="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <app-card class="title-bar" v-if="signalDetail_information">
                        <h4 class="text-capitalize mb-0">
                            Provider:&nbsp;<b>{{ signalDetail_information.provider }}</b> &nbsp;&nbsp;&nbsp;&nbsp;
                            Source&nbsp;Account:&nbsp;<b>{{ signalDetail_information.account_number }}</b>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            Since:&nbsp;<b>{{ getDateFormat(signalDetail_information.openTime) }}</b>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            Copied:&nbsp;<b>{{ signalDetail_information.copier_number }}</b>&nbsp;Times
                            &nbsp;&nbsp;&nbsp;&nbsp;
                        </h4>
                    </app-card>
                    <v-data-table :headers="headers" :items="signalDetail_data" :search="search" item-key="email"
                        :server-items-length="signalDetail_total" :options.sync="options"
                        :loading="signalDetail_loading" :footer-props="{showFirstLastPage: true,}"
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
                                <td>{{ getDateFormat(props.item.created_at) }}</td>
                                <td>{{ props.item.symbol }}</td>
                                <td style="text-transform: uppercase;">{{ props.item.type }}</td>
                                <td>{{ props.item.lots }}</td>
                                <td>{{ props.item.openPrice }}</td>
                                <td>{{ props.item.takeProfitPrice}}</td>
                                <td>{{ props.item.stopLossPrice }}</td>
                                <td>{{ props.item.ticket }}</td>
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
                    { text: "Time", sortable: false },
                    { text: "Symbol", sortable: false },
                    { text: "Trade Type", sortable: false },
                    { text: "Volumn", sortable: false },
                    { text: "Entry Price", sortable: false },
                    { text: "Take Profit", sortable: false },
                    { text: "Stoploss", sortable: false },
                    { text: "Ticket", sortable: false },
                ],
                options: {}
            };
        },
        mounted() {
        },
        methods: {
            ...mapActions([
                'signalDetailAction'
            ]),
            ...{
                getDateFormat(date) {
                    if (!date) return '';
                    let dateObj = new Date(date);
                    return dateformat(dateObj, "mmm, dd yyyy HH:MM")
                }
            }
        },
        computed: {
            ...mapGetters([
                "signalDetail_data",
                "signalDetail_perPage",
                "signalDetail_total",
                "signalDetail_page",
                "signalDetail_information",
                "signalDetail_loading"
            ]),
        },

        watch: {
            options: function (options) {
                this.signalDetailAction({
                    page: options.page,
                    perPage: options.itemsPerPage,
                    account_number: this.$route.params.account_number,
                    broker: this.$route.params.broker,
                });
            }
        }
    };
</script>