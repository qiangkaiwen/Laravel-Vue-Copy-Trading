<template>
    <div class="lock-screen-wrapper">
        <v-container pt-70 px-0>
            <v-row row wrap>
                <v-col xs="12" sm="8" md="4" class="mx-auto">
                    <div class="mb-3">
                        <router-link to="/" class="d-block text-center">
                            <img src="/static/img/White on Transparent.png" class="img-responsive mb-4" style="width: 100%; height: auto;" />
                        </router-link>
                    </div>
                    <div class="lock-screen-block text-center">
                        <div class="s-user mb-3">
                            <img class="rounded-circle img-responsive" src="/static/avatars/user-9.jpg" width="143"
                                height="143">
                        </div>
                        <h2 class="white--text">Jerry Cummings</h2>
                        <v-form v-model="valid">
                            <v-text-field label="Password" v-model="password" :rules="passwordRules" type="password"
                                color="white" required>
                            </v-text-field>
                        </v-form>
                    </div>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>
<script>     export default { data: function () { return { password: "", valid: false, passwordRules: [v => !!v || "Password is required"] }; }, methods: { onSubmit(evt) { evt.preventDefault(); } } };
</script>