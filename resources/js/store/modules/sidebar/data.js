// Sidebar Routers
export const menus = {
	'message.admin': [
		{
			action: 'zmdi-view-dashboard',
			title: 'message.usersList',
			label: 'Old',
			path: '/users-list',
			exact: true
		},
		{
			action: 'zmdi-graphic-eq',
			title: 'message.statistics',
			label: 'Old',
			path: '/statistics',
			exact: true
		},
	],
	'message.user': [
		{
			action: 'zmdi-assignment-account',
			title: 'message.tradingAccounts',
			label: 'Old',
			path: '/trading-accounts',
			exact: true
		},
		{
			action: 'zmdi-share',
			title: 'message.provideSignal',
			label: 'Old',
			path: '/provide-signal',
			exact: true
		},
		{
			action: 'zmdi-cloud-download',
			title: 'message.copySignal',
			label: 'Old',
			path: '/copy-signal',
			exact: true
		},
	],
}
