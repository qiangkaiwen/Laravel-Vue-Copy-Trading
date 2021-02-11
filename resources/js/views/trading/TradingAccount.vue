<template>
    <div>
        <page-title-bar></page-title-bar>
        <app-section-loader :status="account_loading"></app-section-loader>
        <v-container fluid class="grid-list-xl pt-0 mt-n3">
            <v-row>
                <app-card :fullBlock="true" colClasses="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <!-- <v-data-table :headers="headers" :items="users_data" :search="search" item-key="email"
                        :server-items-length="users_total" :options.sync="options" :loading="users_loading"
                        :footer-props="{showFirstLastPage: true,}" :items-per-page-options="[5, 10, 15, 20, -1]">
                        <template slot="headerCell" slot-scope="props" loading-text="Loading... Please wait">
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
                                <td>
                                    <router-link :to="{ name: 'user-profile', params: { user_id: props.item.id } }">
                                        {{props.item.name}}
                                    </router-link>
                                </td>
                                <td>
                                    <router-link :to="{ name: 'user-profile', params: { user_id: props.item.id } }">
                                        {{ props.item.email }}
                                    </router-link>
                                </td>
                                <td>{{ props.item.phone }}</td>
                                <td>{{ props.item.active }}</td>
                            </tr>
                        </template>
                    </v-data-table> -->
                </app-card>
            </v-row>
        </v-container>
    </div>
</template>

<script>
    import { mapGetters, mapActions } from "vuex";
    export default {
        data() {
            return {
                account_loading: false
            };
        },
        mounted() {
        },
        methods: {

        },
    };
</script>