export const routes = [
    {
        path: '/',
        name:'Index',
        component: () => import('./components/pages/Index.vue'),
    },
    {
        path: '/signin',
        name: 'SignIn',
        component: () => import('./components/pages/Login.vue'),
    },
    {
        path: '/admins/events',
        name: 'AdminsEventsIndex',
        component: () => import('./components/pages/admins/EventsIndex.vue'),
    },
    {
        path: '/admins/events/:id',
        name: 'AdminsEventsShow',
        component: () => import('./components/pages/admins/EventsShow.vue'),
    },
    {
        path: '/admins/locations/:id',
        name: 'AdminsLocationsShow',
        component: () => import('./components/pages/admins/LocationsShow.vue'),
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: () => import('./components/pages/Dashboard.vue'),
    }
];
