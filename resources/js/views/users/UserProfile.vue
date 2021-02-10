<template>
    <div>
        <page-title-bar></page-title-bar>
        <v-container fluid class="grid-list-xl pt-0 mt-n3">
            <v-row>
                <v-col cols="12" md="12" lg="12" sm="12" class="col-height-auto">
                    <div v-if="loading">
                        <div class="profile-head app-card mb-30 mt-30">
                            <div class="user-list-content">
                                <div class="text-center">
                                    <h3 class="fw-bold">Loading ...</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div v-if="user" class="profile-head app-card mb-30">
                            <div class="profile-top">
                                <img src="/static/img/profile-banner.jpg" alt="profile banner" width="1920"
                                    height="150" />
                            </div>
                            <div class="profile-bottom border-bottom-light-1">
                                <div class="user-image text-center mb-4">
                                    <img src="/static/avatars/user-7.jpg" width="150" height="150"
                                        class="img-responsive rounded-circle" alt="user images" />
                                </div>
                                <div class="user-list-content">
                                    <div class="text-center">
                                        <h3 class="fw-bold">{{ user.name }}</h3>
                                        <p>{{ user.email }}</p>
                                        <p>{{ user.phone }}</p>
                                        <div class="social-list clearfix mb-5">
                                            <ul class="list-inline d-inline-block">
                                                <li>
                                                    <a href="javascript:void(0);" class="pink--text"><i
                                                            class="ti-facebook"></i></a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);" class="pink--text"><i
                                                            class="ti-twitter-alt"></i></a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);" class="pink--text"><i
                                                            class="ti-google"></i></a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);" class="pink--text"><i
                                                            class="ti-linkedin"></i></a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="clearfix mb-5">
                                            <v-btn class="mr-1" icon @click="onEditUser()">
                                                <v-icon class="grey--text font-lg">edit</v-icon>
                                            </v-btn>
                                            <v-btn icon @click="onDeleteUser()">
                                                <v-icon class="grey--text font-lg">delete</v-icon>
                                            </v-btn>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="user-activity text-center">
                                <ul class="list-inline d-inline-block">
                                    <li>
                                        <span class="fw-bold">Member Since</span>
                                        <span>{{ getFormattedDate(user.created_at) }}</span>
                                    </li>
                                    <li>
                                        <span class="fw-bold">Status</span>
                                        <span>Active</span>
                                    </li>
                                    <li>
                                        <span class="fw-bold">Level</span>
                                        <span>{{ user.level }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div v-else class="profile-head app-card mb-30 mt-30">
                            <div class="user-list-content">
                                <div class="text-center">
                                    <h3 class="fw-bold">User data is not available</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </v-col>
            </v-row>
            <delete-confirmation-dialog ref="deleteConfirmationDialog" heading="Are You Sure You Want To Delete?"
                message="Are you sure you want to delete this User permanently?" @onConfirm="deleteUser">
            </delete-confirmation-dialog>
            <template>
                <v-dialog v-model="open" max-width="600">
                    <v-card class="pa-6">
                        <v-form v-model="form.valid" ref="form" lazy-validation>
                            <v-text-field label="Name" v-model="form.name" :rules="form.nameRules" :counter="30"
                                required></v-text-field>
                            <v-text-field label="E-mail" v-model="form.email" :rules="form.emailRules" required>
                            </v-text-field>
                            <v-text-field label="Phone" v-model="form.phone" required>
                            </v-text-field>
                            <v-text-field label="Password" v-model="form.password" type="password" required>
                            </v-text-field>
                            <v-btn @click="editUser" :disabled="!form.valid" color="success" class="mr-3">
                                Update
                            </v-btn>
                            <v-btn color="primary" @click="open = false" class="px-4">Cancel</v-btn>
                        </v-form>
                    </v-card>
                </v-dialog>
            </template>
        </v-container>
    </div>
</template>

<script>
    import UserDetail from "Components/Widgets/UserDetail";
    import Skills from "Components/Widgets/Skills";
    import Education from "Components/Widgets/Education";
    import ContactRequest from "Components/Widgets/ContactRequest";
    import UserActivity from "Components/Widgets/UserActivity";
    import webServices from "WebServices";
    import dateformat from "dateformat";
    import axios from "axios";

    export default {
        props: ["user_id"],
        data() {
            return {
                user: null,
                loading: false,
                open: false,
                form: {
                    valid: true,
                    name: "",
                    nameRules: [
                        v => !!v || "Name is required",
                        v => (v && v.length <= 30) || "Name must be less than 30 characters"
                    ],
                    email: "",
                    emailRules: [
                        v => !!v || "E-mail is required",
                        v =>
                            /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) ||
                            "E-mail must be valid"
                    ],
                    phone: null,
                    password: false
                }
            };
        },
        mounted() {
            this.loading = true;
            axios
                .get(`${webServices.baseURL}/users/${this.user_id}`, {
                    headers: { "Content-Type": "application/json" },
                })
                .then(({ data }) => {
                    if (data.response.api_status) this.user = data.response.user;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        methods: {
            getFormattedDate(date) {
                return dateformat(new Date(date), "longDate");
            },
            onDeleteUser() {
                this.$refs.deleteConfirmationDialog.openDialog();
            },
            deleteUser() {
                console.log('confirm');
                axios.delete(`${webServices.baseURL}/users/${this.user_id}`).then(() => {
                    this.$router.push('/users-list');
                    this.$refs.deleteConfirmationDialog.close();
                })
            },
            onEditUser() {
                this.openDialog();
            },
            editUser() {
                if (this.$refs.form.validate()) {
                    const { name, email, phone, password } = this.form;
                    let userdata = {
                        name,
                        email,
                    };
                    if (phone && phone != '') {
                        userdata = { ...userdata, ...{ phone } }
                    }
                    if (password && password != '') {
                        userdata = { ...userdata, ...{ password } }
                    }
                    axios.patch(`${webServices.baseURL}/users/${this.user_id}`, userdata).then(() => {
                        this.user = { ...this.user, ...userdata };
                        this.open = false;
                    })
                }
            },
            openDialog() {
                this.form.name = this.user.name;
                this.form.email = this.user.email;
                this.form.phone = this.user.phone;
                this.form.password = this.user.password;

                this.open = true;
            },
        },
    };
</script>