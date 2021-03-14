<template>
    <div>
        <page-title-bar></page-title-bar>
        <app-section-loader :status="settings_loading"></app-section-loader>
        <v-container fluid class="grid-list-xl pt-0 mt-n3">
            <v-row>
                <v-col cols="12" sm="6" md="6" lg="6">
                    <v-card class="elevation-5 px-3 py-3">
                        <img :src="appLogo" alt="Card Image" class="img-responsive" />
                        <v-card-title>
                            <h3 class="headline primary--text">White Logo</h3>
                        </v-card-title>
                        <v-card-actions>
                            <input type="file" accept="image/*" ref="whitelogoImage" style="display: none"
                                @change="changeWhiteLogo">
                            <v-btn class="px-4" color="success" @click="$refs.whitelogoImage.click()">Change</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="6" lg=6>
                    <v-card class="elevation-5 px-3 py-3">
                        <img :src="darkLogo" alt="Card Image" class="img-responsive" />
                        <v-card-title>
                            <h3 class="headline primary--text">Black Logo</h3>
                        </v-card-title>
                        <v-card-actions>
                            <input type="file" accept="image/*" ref="blacklogoImage" style="display: none"
                                @change="changeBlackLogo">
                            <v-btn class="px-4" color="success" @click="$refs.blacklogoImage.click()">Change</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
    import { mapGetters, mapActions } from "vuex";
    import webServices from "WebServices";
    import dateformat from "dateformat";
    import axios from "axios";
    import Vue from "vue";
    import Nprogress from 'nprogress';

    export default {
        data() {
            return {
                settings_loading: false,
            };
        },
        mounted() {
        },
        computed: {
            ...mapGetters(["appLogo", "darkLogo"]),
        },
        methods: {
            ...mapActions(['setWhiteLogo', 'setBlackLogo']),
            ...{
                changeWhiteLogo(event) {
                    var files = event.target.files || event.dataTransfer.files;
                    if (!files.length)
                        return;
                    var formData = new FormData();
                    formData.append("whiteLogo", files[0]);
                    Nprogress.start();
                    axios.post(`${webServices.baseURL}/logo/white`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(({ data }) => {
                        Vue.notify({
                            group: 'signals',
                            type: 'success',
                            text: 'Successfully Updated!'
                        });
                        const filename = data.response.filename;
                        this.setWhiteLogo(filename);
                    }).catch(() => {
                        Vue.notify({
                            group: 'signals',
                            type: 'error',
                            text: 'Upload failed'
                        });
                    }).finally(() => {
                        Nprogress.done();
                    });
                },
                changeBlackLogo(event) {
                    var files = event.target.files || event.dataTransfer.files;
                    if (!files.length)
                        return;
                    var formData = new FormData();
                    formData.append("blackLogo", files[0]);
                    Nprogress.start();
                    axios.post(`${webServices.baseURL}/logo/black`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(({ data }) => {
                        Vue.notify({
                            group: 'signals',
                            type: 'success',
                            text: 'Successfully Updated!'
                        });
                        const filename = data.response.filename;
                        this.setBlackLogo(filename);
                    }).catch(() => {
                        Vue.notify({
                            group: 'signals',
                            type: 'error',
                            text: 'Upload failed'
                        });
                    }).finally(() => {
                        Nprogress.done();
                    });
                }
            }
        },
    };
</script>