<template>
    <div>
        <page-title-bar></page-title-bar>
        <app-section-loader :status="accounts_loading"></app-section-loader>
        <v-container fluid class="grid-list-xl pt-0 mt-n3">
            <v-row>
                <app-card :fullBlock="true" colClasses="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
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
                                <td>{{props.item.account_number}}</td>
                                <td>{{ props.item.authorization }}</td>
                                <td>{{ props.item.expiry }}</td>
                                <td>{{ props.item.online_status }}</td>
                                <td>{{ props.item.lots_traded }}</td>
                                <td>{{ props.item.created_at }}</td>
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
    import dateformat from 'dateformat';

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
                    { text: "Authorization", value: "authorization", sortable: false },
                    { text: "Expiry", value: "expiry", sortable: false },
                    { text: "Online Status", value: "online_status", sortable: false },
                    { text: "Lots Traded", value: "lots_traded", sortable: false },
                    { text: "Created At", value: "created_at", sortable: false },
                ],
                options: {}
            };
        },
        mounted() {
        },
        methods: {
            ...mapActions([
                'getAccountsAction'
            ]),
            ...{
                convertDate(date) {
                    if (!date) return '';
                    return dateformat(new Date(date), "mediumDate");
                },
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
        },

        watch: {
            options: function (options) {
                this.getAccountsAction({ page: options.page, perPage: options.itemsPerPage });
            }
        }
    };
</script>