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
                            <div class="user-profile-widget top-author-wrap">
                                <div class="avatar-wrap mb-50 pos-relative">
                                    <span class="overlay-content"></span>
                                    <div class="user-info">
                                        <img src="/static/avatars/user-7.jpg" alt="reviwers" width="100" height="100"
                                            class="img-responsive rounded-circle mr-3">
                                        <div class="white--text pt-7">
                                            <h1 class="mb-0" style="text-transform: uppercase;">{{ user.name }}</h1>
                                        </div>
                                    </div>
                                </div>
                                <div class="author-detail-wrap">
                                    <div class="pa-3 authors-info">
                                        <ul class="list-unstyled author-contact-info mb-2">
                                            <li class="d-flex px-4 align-center">
                                                <span class="mr-3 d-custom-flex align-items-left w-30">
                                                    <span><i class="zmdi zmdi-email"></i> Email</span>
                                                </span>
                                                <span
                                                    class="fs-20 grey--text fw-normal d-custom-flex align-items-left w-70">{{
                                                    user.email }}</span>
                                            </li>
                                            <li class="d-flex px-4 align-center">
                                                <span class="mr-3 d-custom-flex align-items-left w-30">
                                                    <span><i class="zmdi zmdi-phone-msg"></i> Phone</span>
                                                </span>
                                                <span
                                                    class="fs-20 grey--text fw-normal d-custom-flex align-items-left w-70">{{
                                                    user.phone }}</span>
                                            </li>
                                            <li class="d-flex px-4 align-center">
                                                <span class="mr-3 d-custom-flex align-items-left w-30">
                                                    <span><i class="zmdi zmdi-calendar-alt"></i> Birthday</span>
                                                </span>
                                                <span
                                                    class="fs-20 grey--text fw-normal d-custom-flex align-items-left w-70">{{
                                                    getFormattedDate(user.date_of_birth) }}</span>
                                            </li>
                                            <li class="d-flex px-4 align-center">
                                                <span class="mr-3 d-custom-flex align-items-left w-30">
                                                    <span><i class="zmdi zmdi-calendar-check"></i> Member Since</span>
                                                </span>
                                                <span
                                                    class="fs-20 grey--text fw-normal d-custom-flex align-items-left w-70">{{
                                                    getFormattedDate(user.created_at) }}</span>
                                            </li>
                                        </ul>
                                        <ul class="d-custom-flex social-info list-unstyled">
                                            <li><a class="facebook" href="www.facebook.com"><i
                                                        class="zmdi zmdi-facebook-box"></i></a></li>
                                            <li><a class="twitter" href="www.twitter.com"><i
                                                        class="zmdi zmdi-twitter-box"></i></a></li>
                                            <li><a class="linkedin" href="www.linkedin.com"><i
                                                        class="zmdi zmdi-linkedin-box"></i></a></li>
                                            <li><a class="instagram" href="www.instagram.com"><i
                                                        class="zmdi zmdi-instagram"></i></a></li>
                                        </ul>
                                    </div>
                                    <div class="d-custom-flex align-center px-3 pb-3">
                                        <v-btn fab class="mr-3" dark color="cyan" @click="onEditUser()">
                                            <v-icon dark>edit</v-icon>
                                        </v-btn>
                                        <v-btn fab @click="onDeleteUser()"  dark color="red">
                                            <v-icon class="grey--text font-lg">delete</v-icon>
                                        </v-btn>
                                    </div>
                                    <ul
                                        class="d-custom-flex list-unstyled footer-content text-center w-100 border-top-1 align-end">
                                        <li>
                                            <h5 class="mb-0">80</h5>
                                            <span class="fs-12 grey--text fw-normal">Providers</span>
                                        </li>
                                        <li>
                                            <h5 class="mb-0">150</h5>
                                            <span class="fs-12 grey--text fw-normal">Followers</span>
                                        </li>
                                        <li>
                                            <h5 class="mb-0">2k</h5>
                                            <span class="fs-12 grey--text fw-normal">Copiers</span>
                                        </li>
                                    </ul>
                                </div>
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
                if (!date) return '';
                return dateformat(new Date(date), "longDate");
            },
            onDeleteUser() {
                this.$refs.deleteConfirmationDialog.openDialog();
            },
            deleteUser() {
                axios.delete(`${webServices.baseURL}/users/${this.user_id}`).then(() => {
                    this.$refs.deleteConfirmationDialog.close();
                    this.$router.push('/users-list');
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