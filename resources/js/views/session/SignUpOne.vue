<template>
	<div class="session-wrapper">
		<div class="session-left">
			<session-slider-widget></session-slider-widget>
		</div>
		<div class="session-right text-center">
			<div class="session-table-cell-signup">
				<div class="session-content">
					<img :src="appLogoF" class="img-responsive mb-4" style="width: 70%; height: auto;" />
					<h2 class="mb-4">{{$t('message.signUp')}}</h2>
					<p class="fs-14">{{$t('message.havingAnAccount')}}
						<router-link to="/session/login">{{$t('message.login')}}</router-link>
					</p>
					<v-form v-model="valid" class="mb-5">
						<v-text-field label="Username" v-model="name" :rules="nameRules" :counter="30" required>
						</v-text-field>
						<v-text-field label="E-mail" v-model="email" :rules="emailRules" required></v-text-field>
						<v-menu ref="dateref" :close-on-content-click="false" v-model="dateref"
							transition="scale-transition" offset-y :nudge-right="40" min-width="290px"
							:return-value.sync="date">
							<template v-slot:activator="{ on }">
								<v-text-field v-on="on" label="Date Of Birth" v-model="date" prepend-icon="event"
									readonly></v-text-field>
							</template>
							<v-date-picker v-model="date" no-title scrollable>
								<v-spacer></v-spacer>
								<v-btn color="primary" @click="dateref = false">Cancel</v-btn>
								<v-btn color="warning" @click="$refs.dateref.save(date)">OK</v-btn>
							</v-date-picker>
						</v-menu>
						<v-text-field label="Phone Number" v-model="phone" type="text" required></v-text-field>
						<v-text-field label="Password" v-model="password" :rules="passwordRules" type="password"
							required></v-text-field>
						<v-btn large @click="signupWithLaravel" block color="primary" class="mb-3">
							{{$t('message.signUp')}} </v-btn>
						<router-link to="">{{$t('message.termsOfService')}}</router-link>
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
				valid: false,
				name: "",
				nameRules: [
					v => !!v || "Name is required",
					v => v.length <= 30 || "Name must be less than 30 characters"
				],
				email: "",
				emailRules: [
					v => !!v || "E-mail is required",
					v =>
						/^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) ||
						"E-mail must be valid"
				],
				password: "",
				passwordRules: [v => !!v || "Password is required"],
				brand: AppConfig.brand,
				phone: "",
				dateref: false,
				date: null,
			};
		},
		computed: {
			...mapGetters(["appLogo", "darkLogo"]),
			...{
				appLogoF() {
					if (this.$vuetify.theme.dark)
						return this.appLogo;
					return this.darkLogo;
				}
			}
		},
		methods: {
			signupWithLaravel() {
				let userDetail = {
					name: this.name,
					email: this.email,
					password: this.password,
					date_of_birth: this.date,
					phone: this.phone,
				};
				this.$store.dispatch("signupUserWithLaravelPassport", {
					userDetail
				});
			},

		}
	};
</script>