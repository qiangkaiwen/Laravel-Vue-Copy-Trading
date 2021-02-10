// Sidebar Routers
export const menus = {
	'message.admin': [
		{
			action: 'zmdi-view-dashboard',
			title: 'message.usersList',
			// active: true,
			label: 'Old',
			path: '/users-list',
			exact: true
			// items: [
				// { title: 'message.usersList', path: '/users-list', exact: true, label: 'Old' },
				// { title: 'message.userProfile', path: '/user-profile', label: 'Old', exact: true },
			// ]
		},
		{
			action: 'zmdi-graphic-eq',
			title: 'message.statistics',
			// active: true,
			label: 'Old',
			path: '/statistics',
			exact: true
		},
	],
	'message.user': [
		{
			action: 'zmdi-assignment-account',
			title: 'message.tradingAccounts',
			// active: true,
			label: 'Old',
			path: '/trading-accounts',
			exact: true
		},
		{
			action: 'zmdi-share',
			title: 'message.provideSignal',
			// active: true,
			label: 'Old',
			path: '/provide-signal',
			exact: true
		},
		{
			action: 'zmdi-cloud-download',
			title: 'message.copySignal',
			// active: true,
			label: 'Old',
			path: '/copy-signal',
			exact: true
		},
	],
}
