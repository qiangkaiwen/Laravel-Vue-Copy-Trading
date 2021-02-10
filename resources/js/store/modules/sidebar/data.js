// Sidebar Routers
export const menus = {
	'message.general': [
		{
			action: 'zmdi-view-dashboard',
			title: 'message.users',
			active: true,
			label: 'Old',
			items: [
				{ title: 'message.usersList', path: '/users-list', exact: true, label: 'Old' },
				// { title: 'message.userProfile', path: '/user-profile', label: 'Old', exact: true },
			]
		},
	],
}
