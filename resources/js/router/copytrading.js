import Full from 'Container/Full'

// CRM components
const UserProfile = () => import('Views/users/UserProfile');
const UsersList = () => import('Views/users/UsersList');

export default {
    path: '/',
    component: Full,
    redirect: '/users-list',
    children: [
        // users
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
                        breadcrumbInactive: 'Users /'
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
                        breadcrumbInactive: 'Users /'
                    },
                    {
                        breadcrumbActive: 'Users List'
                    }
                ]
            }
        },
    ]
}
