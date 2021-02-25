// Sidebar Routers
export const menus = {
	'message.admin': [
		{
			action: 'zmdi-accounts-list',
			title: 'message.usersList',
			label: 'Old',
			path: '/users-list',
			exact: true,
			tooltip: 'Show Users List',
		},
		{
			action: 'zmdi-graphic-eq',
			title: 'message.statistics',
			label: 'Old',
			path: '/statistics',
			exact: true,
			tooltip: 'Show Statistics',
		},
	],
	'message.user': [
		{
			action: 'zmdi-assignment-account',
			title: 'message.tradingAccounts',
			label: 'Old',
			path: '/trading-accounts',
			exact: true,
			tooltip: 'Show Your accounts',
		},
		{
			action: 'zmdi-share',
			title: 'message.provideSignal',
			label: 'Old',
			path: '/provide-signal',
			exact: true,
			tooltip: 'Show Your provided accounts',
		},
		{
			action: 'zmdi-cloud-download',
			title: 'message.copySignal',
			label: 'Old',
			path: '/copy-signal',
			exact: true,
			tooltip: 'Show Your copying accounts',
		},
		{
			action: 'zmdi-view-list',
			title: 'message.availableSignals',
			label: 'Old',
			path: '/available-signal',
			exact: true,
			tooltip: 'Show Available accounts you can copy',
		},
		{
			action: 'zmdi-account-box',
			title: 'message.myProfile',
			label: 'Old',
			path: '/my-profile',
			exact: true,
			tooltip: 'Show Your Profile',
		},
	],
}
