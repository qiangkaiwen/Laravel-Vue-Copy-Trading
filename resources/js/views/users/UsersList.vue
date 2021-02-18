<template>
    <div>
        <page-title-bar></page-title-bar>
        <app-section-loader :status="users_loading"></app-section-loader>
        <v-container fluid class="grid-list-xl pt-0 mt-n3">
            <v-row>
                <v-tabs class="reports-table-tab" v-model="active" slider-color="primary">
                    <v-tab class="text-capitalize" href="#existingusers">
                        {{ $t('message.existingUsers') }}
                    </v-tab>
                    <v-tab class="text-capitalize" href="#newusers">
                        {{ $t('message.newUsers') }}
                    </v-tab>
                    <v-tab-item value="existingusers">
                        <app-card :fullBlock="true" colClasses="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <v-data-table :key="usersTableKey" :headers="headers" :items="users_data" :search="search"
                                item-key="email" :server-items-length="users_total" :options.sync="options"
                                :loading="users_loading" :footer-props="{showFirstLastPage: true,}"
                                :items-per-page-options="[5, 10, 15, 20, -1]">
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
                                        <td>{{ props.item.id }}</td>
                                        <td>{{props.item.name}}</td>
                                        <td>
                                            <v-list-item-avatar>
                                                <img src="/static/avatars/user-32.jpg" alt="avatar" height="30"
                                                    width="30" class="img-responsive" />
                                            </v-list-item-avatar>
                                        </td>
                                        <td>
                                            {{ props.item.email }}
                                        </td>
                                        <td>{{ props.item.phone }}</td>
                                        <td>{{ convertDate(props.item.created_at) }}</td>
                                        <td>
                                            <router-link
                                                :to="{ name: 'user-profile', params: { user_id: props.item.id } }">
                                                <v-btn text icon color="primary">
                                                    <v-icon class="zmdi zmdi-eye"></v-icon>
                                                </v-btn>
                                            </router-link>
                                            <v-btn text icon color="error" @click="deleteUser(props.item.id)">
                                                <v-icon class="zmdi zmdi-delete"></v-icon>
                                            </v-btn>
                                        </td>
                                    </tr>
                                </template>
                            </v-data-table>
                        </app-card>
                    </v-tab-item>
                    <v-tab-item value="newusers">
                        <app-card :fullBlock="true" colClasses="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <v-data-table :key="newUsersTableKey" :headers="headers" :items="new_users_data" :search="search" item-key="email"
                                :server-items-length="new_users_total" :options.sync="new_options"
                                :loading="new_users_loading" :footer-props="{showFirstLastPage: true,}"
                                :items-per-page-options="[5, 10, 15, 20, -1]">
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
                                        <td>{{ props.item.id }}</td>
                                        <td>{{props.item.name}}</td>
                                        <td>
                                            <v-list-item-avatar>
                                                <img src="/static/avatars/user-31.jpg" alt="avatar" height="30"
                                                    width="30" class="img-responsive" />
                                            </v-list-item-avatar>
                                        </td>
                                        <td>
                                            <router-link
                                                :to="{ name: 'user-profile', params: { user_id: props.item.id } }">
                                                {{ props.item.email }}
                                            </router-link>
                                        </td>
                                        <td>{{ props.item.phone }}</td>
                                        <td>{{ convertDate(props.item.created_at) }}</td>
                                        <v-btn text icon color="success" @click="activeUser(props.item.id)">
                                            <v-icon class="zmdi zmdi-check-circle-u"></v-icon>
                                        </v-btn>
                                        <v-btn text icon color="red" @click="blockUser(props.item.id)">
                                            <v-icon class="zmdi zmdi-block-alt"></v-icon>
                                        </v-btn>
                                    </tr>
                                </template>
                            </v-data-table>
                        </app-card>
                    </v-tab-item>
                </v-tabs>
            </v-row>
            <delete-confirmation-dialog ref="deleteConfirmationDialog" heading="Are You Sure You Want To Delete?"
                message="Are you sure you want to delete this User permanently?" @onConfirm="deleteUserConfirm">
            </delete-confirmation-dialog>
            <confirmation-dialog ref="activeConfirmationDialog" heading="Are You Sure You Want To Activate?"
                message="Are you sure you want to active this User?" @onConfirm="activeUserConfirm"
                confirmColor="success">
            </confirmation-dialog>
            <confirmation-dialog ref="blockConfirmationDialog" heading="Are You Sure You Want To Block?"
                message="Are you sure you want to block this User?" @onConfirm="blockUserConfirm" confirmColor="red">
            </confirmation-dialog>
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
                usersTableKey: 0,
                newUsersTableKey: 0,
                search: "",
                headers: [
                    {
                        text: "ID",
                        align: "left",
                        sortable: false,
                        value: 'id'
                    },
                    { text: "Name", value: "name", sortable: false },
                    { text: "Avatar", value: "name", sortable: false },
                    { text: "Email", value: "email", sortable: false },
                    { text: "Phone", value: "phone", sortable: false },
                    { text: "Joined In", value: "created_at", sortable: false },
                    { text: "", value: "created_at", sortable: false },
                ],
                options: {},
                new_options: {},
                active: 'existingusers',
                delete_id: null,
                active_id: null,
                block_id: null,
            };
        },
        mounted() {
        },
        methods: {
            ...mapActions([
                'getUsersAction',
                'getNewUsersAction',
            ]),
            ...{
                convertDate(date) {
                    if (!date) return '';
                    return dateformat(new Date(date), "mediumDate");
                },
                deleteUser(id) {
                    this.delete_id = id;
                    this.$refs.deleteConfirmationDialog.openDialog();
                },
                deleteUserConfirm() {
                    axios.delete(`${webServices.baseURL}/users/${this.delete_id}`).then(() => {
                        this.$refs.deleteConfirmationDialog.close();
                        this.usersTableKey++;
                    })
                },
                activeUser(id) {
                    this.active_id = id;
                    this.$refs.activeConfirmationDialog.openDialog();
                },
                activeUserConfirm() {
                    axios.patch(`${webServices.baseURL}/users/${this.active_id}`, { active: 1 }).then(() => {
                        this.$refs.activeConfirmationDialog.close();
                        this.usersTableKey++;
                        this.newUsersTableKey++;
                    });
                },
                blockUser(id) {
                    this.block_id = id;
                    this.$refs.blockConfirmationDialog.openDialog();
                },
                blockUserConfirm() {
                    axios.patch(`${webServices.baseURL}/users/${this.block_id}`, { active: -1 }).then(() => {
                        this.$refs.blockConfirmationDialog.close();
                        this.newUsersTableKey++;
                    });
                }
            }
        },
        computed: {
            ...mapGetters([
                "users_data",
                "users_perPage",
                "users_total",
                "users_page",
                "users_loading",

                "new_users_data",
                "new_users_perPage",
                "new_users_total",
                "new_users_page",
                "new_users_loading",
            ]),
        },

        watch: {
            options: function (options) {
                this.getUsersAction({ page: options.page, perPage: options.itemsPerPage });
            },
            new_options: function (options) {
                this.getNewUsersAction({ page: options.page, perPage: options.itemsPerPage });
            },
        }
    };
</script>