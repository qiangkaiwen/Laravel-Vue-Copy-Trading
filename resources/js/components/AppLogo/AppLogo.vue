<template>
    <div class="site-logo mt-2">
        <router-link to="/">
            <img :src="darkLogo" alt="site logo" style="width: 100%; height: auto;"
                v-if="sidebarSelectedFilter.class == 'sidebar-overlay-light'">
            <img :src="appLogo" alt="site logo" style="width: 100%; height: auto;" v-else>
        </router-link>
    </div>
</template>

<script>
    import AppConfig from "Constants/AppConfig";
    import { mapGetters } from "vuex";

    export default {
        computed: {
            ...mapGetters(["sidebarSelectedFilter"])
        },
        data() {
            return {
                appLogo: AppConfig.appLogo,
                darkLogo: AppConfig.darkLogo
            }
        }
    }
</script>