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
                                <td>{{props.item.account_number}}</td>
                                <td>{{ props.item.broker }}</td>
                                <td>
                                    <v-badge :value=false :class="props.item.statusColor">{{ props.item.status }}
                                    </v-badge>
                                </td>
                                <td>{{ convertDate(props.item.created_at) }}</td>
                                <td>
                                    <router-link v-if="props.item.status == 'PROVIDE'"
                                        :to="{ name: 'signal-detail', params: { account_number: props.item.account_number, broker: props.item.broker } }">
                                        <v-btn text icon color="primary">
                                            <v-icon class="zmdi zmdi-eye"></v-icon>
                                        </v-btn>
                                    </router-link>
                                    <router-link v-if="props.item.status == 'COPY'"
                                        :to="{ name: 'copy-signal', hash: '#' + props.item.account_number }">
                                        <v-btn text icon color="primary">
                                            <v-icon class="zmdi zmdi-eye"></v-icon>
                                        </v-btn>
                                    </router-link>
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
                    { text: "Broker", value: "account_number", sortable: false },
                    { text: "Status", value: "online_status", sortable: false },
                    { text: "Created At", value: "created_at", sortable: false },
                    { text: "", sortable: false },
                ],
                options: {},
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