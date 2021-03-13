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
    import { mapGetters } from "vuex";

    export default {
        computed: {
            ...mapGetters(["sidebarSelectedFilter", "appLogo", "darkLogo"])
        },
        data() {
            return {
            }
        }
    }
</script>