<template>
	<div class="session-wrapper">
		<div class="session-left">
			<!-- <session-slider-widget></session-slider-widget> -->
		</div>
		<div class="session-right text-center">
			<div class="session-table-cell">
				<div class="session-content">
					<!-- <img :src="appLogo" class="img-responsive mb-4" width="78" height="78" /> -->
					<!-- <h2 class="mb-4">{{$t('message.loginToAdmin')}}</h2> -->
					<!-- <p class="fs-14">{{$t('message.enterUsernameAndPasswordToAccessControlPanelOf')}} {{brand}}.</p> -->
					<v-form v-model="valid" class="mb-5">
						<v-text-field label="E-mail ID" v-model="email" :rules="emailRules" required></v-text-field>
						<v-text-field label="Password" v-model="password" type="password" :rules="passwordRules"
							required></v-text-field>
						<v-checkbox color="primary" label="Remember me" v-model="checkbox"></v-checkbox>
						<router-link class="mb-2" to="/session/forgot-password">{{$t('message.forgotPassword')}}?
						</router-link>
						<div>
							<v-btn large class="mb-2" @click="signInWithLaravelPassport" block color="primary">
								{{$t('message.loginWithOrigin')}}</v-btn>
							<v-btn large @click="onCreateAccount" block color="warning" class="mb-2">
								{{$t('message.createAccount')}}</v-btn>
						</div>
						<!-- <router-link to="">{{$t('message.termsOfService')}}</router-link> -->
					</v-form>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import SessionSliderWidget from "Components/Widgets/SessionSlider";
	import AppConfig from "Constants/AppConfig";

	export default {
		components: {
			SessionSliderWidget
		},
		data() {
			return {
				checkbox: false,
				valid: false,
				email: "",
				emailRules: [
					v => !!v || "E-mail is required",
					v =>
						/^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) ||
						"E-mail must be valid"
				],
				password: "",
				passwordRules: [v => !!v || "Password is required"],
				appLogo: AppConfig.appLogo2,
				brand: AppConfig.brand
			};
		},
		methods: {
			signInWithLaravelPassport() {
				const user = {
					email: this.email,
					password: this.password
				};
				this.$store.dispatch("signInWithLaravelPassport", {
					user
				});
			},
			onCreateAccount() {
				this.$router.push("/session/sign-up");
			},
		}
	};
</script>