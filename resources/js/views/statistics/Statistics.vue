<template>
    <div>
        <page-title-bar></page-title-bar>
        <app-section-loader :status="statistics_loading"></app-section-loader>
        <v-container fluid class="grid-list-xl pt-0 mt-n3">
            <v-row>
                <app-card :fullBlock="true"
                    colClasses="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
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
                statistics_loading: false
            };
        },
        mounted() {
        },
        methods: {
           
        },
    };
</script>