<template>
	<v-list-item class="sidebar-profile">
		<v-list-item-avatar>
			<img :src="getAvatar" alt="avatar" height="40" width="40" class="img-responsive" />
		</v-list-item-avatar>
		<v-list-item-content class="ml-3">
			<v-list-item-title><span>{{ getUser ? getUser.name : '' }}</span></v-list-item-title>
		</v-list-item-content>
		<v-menu bottom offset-y left content-class="userblock-dropdown" nudge-top="-10" nudge-right="0"
			transition="slide-y-transition">
			<template v-slot:activator="{ on }">
				<v-btn dark icon v-on="on" class="ma-0">
					<v-icon>more_vert</v-icon>
				</v-btn>
			</template>
			<div class="dropdown-content">
				<div class="dropdown-top white--text primary">
					<span class="white--text fs-14 fw-bold d-block">{{ getUser ? getUser.name : '' }}</span>
					<span class="d-block fs-12 fw-normal">{{ getUser ? getUser.email : '' }}</span>
				</div>
				<v-list class="dropdown-list">
					<template v-for="userLink in userLinks">
						<template v-if="userLink.id !== 'logout'">
							<v-list-item :to="getMenuLink(userLink.path)" :key="userLink.id">
								<i :class="userLink.icon"></i>
								<span>{{$t(userLink.title)}}</span>
							</v-list-item>
						</template>
						<template v-else>
							<v-list-item @click="logoutUser" :key="userLink.id">
								<i :class="userLink.icon"></i>
								<span>{{$t(userLink.title)}}</span>
							</v-list-item>
						</template>
					</template>
				</v-list>
			</div>
		</v-menu>
	</v-list-item>
</template>

<script>
	import { getCurrentAppLayout } from "Helpers/helpers";
	import { mapGetters } from "vuex";

	export default {
		data() {
			return {
				userLinks: [
					{
						id: 'profile',
						title: 'message.myProfile',
						path: 'my-profile',
						icon: 'ti-user mr-3 success--text'
					},
					{
						id: 'logout',
						title: 'message.logOut',
						icon: 'ti-power-off mr-3 error--text'
					}
				]
			}
		},
		methods: {
			logoutUser() {
				this.$store.dispatch("signOutWithLaravelPassport", this.$router);
			},
			getMenuLink(path) {
				return '/' + path;
			}
		},
		computed: {
			...mapGetters([
				"getUser",
			]),
			...{
				getAvatar() {
					if (!this.getUser)
						return '/static/avatars/default.png';
					const { avatar } = this.getUser;
					return avatar ? avatar : '/static/avatars/default.png';
				}
			}
		},
	};
</script>