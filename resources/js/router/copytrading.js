import Full from 'Container/Full'

// CRM components
const UserProfile = () => import('Views/users/UserProfile');
const UsersList = () => import('Views/users/UsersList');
const MyProfile = () => import('Views/users/MyProfile');
const Statistics = () => import('Views/statistics/Statistics');
const TradingAccount = () => import('Views/trading/TradingAccount');
const ProvideSignal = () => import('Views/trading/ProvideSignal');
const ProvideSignalDetail = () => import('Views/trading/ProvideSignalDetail');
const CopySignal = () => import('Views/trading/CopySignal');

export default {
    path: '/',
    component: Full,
    redirect: '/users-list',
    children: [
        // users
        {
            path: '/my-profile',
            name: 'my-profile',
            component: MyProfile,
            props: true,
            meta: {
                requiresAuth: true,
                title: 'message.myProfile',
                breadcrumb: [
                    {
                        breadcrumbInactive: 'User /'
                    },
                    {
                        breadcrumbActive: 'My Profile'
                    }
                ]
            }
        },
        {
            path: '/user-profile/:user_id',
            name: 'user-profile',
            component: UserProfile,
            props: true,
            meta: {
                requiresAuth: true,
                title: 'message.userProfile',
                breadcrumb: [
                    {
                        breadcrumbInactive: 'Admin /'
                    },
                    {
                        breadcrumbActive: 'User Profile'
                    }
                ]
            }
        },
        {
            path: '/users-list',
            name: 'users-list',
            component: UsersList,
            meta: {
                requiresAuth: true,
                title: 'message.usersList',
                breadcrumb: [
                    {
                        breadcrumbInactive: 'Admin /'
                    },
                    {
                        breadcrumbActive: 'Users List'
                    }
                ]
            }
        },

        {
            path: '/statistics',
            name: 'statistics',
            component: Statistics,
            meta: {
                requiresAuth: true,
                title: 'message.statistics',
                breadcrumb: [
                    {
                        breadcrumbInactive: 'Admin /'
                    },
                    {
                        breadcrumbActive: 'Statistics'
                    }
                ]
            }
        },

        {
            path: '/trading-accounts',
            name: 'trading-accounts',
            component: TradingAccount,
            meta: {
                requiresAuth: true,
                title: 'message.tradingAccounts',
                breadcrumb: [
                    {
                        breadcrumbInactive: 'User /'
                    },
                    {
                        breadcrumbActive: 'Trading Accounts'
                    }
                ]
            }
        },

        {
            path: '/provide-signal',
            name: 'provide-signal',
            component: ProvideSignal,
            meta: {
                requiresAuth: true,
                title: 'message.provideSignal',
                breadcrumb: [
                    {
                        breadcrumbInactive: 'User /'
                    },
                    {
                        breadcrumbActive: 'Provide Signal'
                    }
                ]
            }
        },

        {
            path: '/provide-signal-detail/:account_number',
            name: 'provide-signal-detail',
            component: ProvideSignalDetail,
            meta: {
                requiresAuth: true,
                title: 'message.provideSignalDetail',
                breadcrumb: [
                    {
                        breadcrumbInactive: 'User /'
                    },
                    {
                        breadcrumbActive: 'Provide Signal Detail'
                    }
                ]
            }
        },

        {
            path: '/copy-signal',
            name: 'copy-signal',
            component: CopySignal,
            meta: {
                requiresAuth: true,
                title: 'message.copySignal',
                breadcrumb: [
                    {
                        breadcrumbInactive: 'User /'
                    },
                    {
                        breadcrumbActive: 'Copy Signal'
                    }
                ]
            }
        },
    ]
}
