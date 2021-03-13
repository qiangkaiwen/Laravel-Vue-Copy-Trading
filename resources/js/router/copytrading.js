import Full from 'Container/Full'

// CRM components
const UserProfile = () => import('Views/users/UserProfile');
const UsersList = () => import('Views/users/UsersList');
const MyProfile = () => import('Views/users/MyProfile');
const Statistics = () => import('Views/statistics/Statistics');
const Settings = () => import('Views/settings/Settings');
const TradingAccount = () => import('Views/trading/TradingAccount');
const ProvideSignal = () => import('Views/trading/ProvideSignal');
const SignalDetail = () => import('Views/trading/SignalDetail');
const copyingSignals = () => import('Views/trading/copyingSignals');
const AvailableSignals = () => import('Views/trading/AvailableSignals');

export default {
    path: '/',
    component: Full,
    redirect: '/trading-accounts',
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
            path: '/settings',
            name: 'settings',
            component: Settings,
            meta: {
                requiresAuth: true,
                title: 'message.settings',
                breadcrumb: [
                    {
                        breadcrumbInactive: 'Admin /'
                    },
                    {
                        breadcrumbActive: 'Settings'
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
            path: '/signal-detail/:account_number/:broker',
            name: 'signal-detail',
            component: SignalDetail,
            meta: {
                requiresAuth: true,
                title: 'message.signalDetail',
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
            path: '/copying-signal',
            name: 'copying-signal',
            component: copyingSignals,
            meta: {
                requiresAuth: true,
                title: 'message.copyingSignals',
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

        {
            path: '/available-sources',
            name: 'available-sources',
            component: AvailableSignals,
            meta: {
                requiresAuth: true,
                title: 'message.availableSources',
                breadcrumb: [
                    {
                        breadcrumbInactive: 'User /'
                    },
                    {
                        breadcrumbActive: 'Available Signals'
                    }
                ]
            }
        },
    ]
}
