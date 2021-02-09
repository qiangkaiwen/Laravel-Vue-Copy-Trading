<template>
    <div>
        <page-title-bar></page-title-bar>
        <app-section-loader :status="loader"></app-section-loader>
        <v-container fluid class="grid-list-xl pt-0 mt-n3">
            <v-row>
                <app-card :heading="$t('message.selectableRows')" :fullBlock="true"
                    colClasses="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <v-data-table :headers="headers" :items="users_data" :search="search" item-key="name">
                        <template slot="headerCell" slot-scope="props">
                            <v-tooltip bottom>
                                <span slot="activator">
                                    {{ props.header.text }}
                                </span>
                                <span>
                                    {{ props.header.text }}
                                </span>
                            </v-tooltip>
                        </template>
                        <template slot="items" slot-scope="props">
                            <td>{{ props.item.name }}</td>
                            <td>{{ props.item.name }}</td>
                            <td>{{ props.item.email }}</td>
                            <td>{{ props.item.phone }}</td>
                            <td>{{ props.item.active }}</td>
                        </template>
                    </v-data-table>
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
                loader: true,
                usersList: null,
                connectUsersList: null,
                search: "",
                headers: [
                    {
                        text: "#",
                        align: "left",
                        sortable: false,
                    },
                    { text: "Name", value: "name" },
                    { text: "Email", value: "email" },
                    { text: "Phone", value: "phone" },
                    { text: "active", value: "active" },
                ],
            };
        },
        mounted() {
            this.getUsers();
        },
        methods: {
            ...mapActions([
                'getUsersAction'
            ]),
            ...{
                getImgSrc(connectedUsers) {
                    if (this.connectUsersList) {
                        for (var i = 0; i < this.connectUsersList.length; i++) {
                            var user = this.connectUsersList[i];
                            if (connectedUsers === user.id) {
                                return user.img;
                            }
                        }
                    }
                },
                getUsers() {
                    this.getUsersAction(1);
                },
            }
        },
        computed: {
            ...mapGetters([
                "users_data",
                "users_perPage",
                "users_total",
                "users_page"
            ]),
        },

        watch: {
            users_data: function(newval) {
                console.log(newval);
            }
        }
    };
</script>