<template>
	<v-container fluid>
		<app-card :fullBlock="true" class="title-bar">
			<v-breadcrumbs class="pa-6 pr-4" :items="breadcrumbItems" v-if="breadcrumbItems != null">
				<template slot="item" slot-scope="props">
					<h2 class="text-capitalize mb-0" v-if="!account_number">{{$t(props.item.title)}}</h2>
					<h2 class="text-capitalize mb-0" v-else>{{$t(props.item.title)}} (&nbsp;Source&nbsp;ID:&nbsp;{{ account_number }}&nbsp;)</h2>
					<div class="spacer"></div>
					<v-breadcrumbs-item>
						<a href="javascript:void(0)">{{props.item.breadcrumb[0].breadcrumbInactive}}</a>
					</v-breadcrumbs-item>
					<v-breadcrumbs-item disabled>
						{{props.item.breadcrumb[1].breadcrumbActive}}
					</v-breadcrumbs-item>
				</template>
			</v-breadcrumbs>
		</app-card>
	</v-container>
</template>

<script>
	export default {
		data() {
			return {
				breadcrumbItems: [],
				user_id: null,
				account_number: null,
			}
		},
		created() {
			this.breadcrumbItems[0] = this.$breadcrumbs[0].meta;
		},
		mounted() {
			this.user_id = this.$route.params.user_id;
			this.account_number = this.$route.params.account_number;
		}
	};
</script>