// app.js
import {createApp} from 'vue';
import {createRouter, createWebHistory} from 'vue-router';
import App from './components/pages/App.vue';

// Define routes
const routes = [
    {name: 'index', path: '/', component: () => import('./components/pages/Home.vue')}, // Home should be loaded on the root route
    {name:'events',path: '/events',component: () => import('./components/pages/Events.vue')},
    {name: 'signup', path: '/signup', component: () => import( './components/pages/Signup.vue')},
    {
        name: 'admin.events',
        path: '/admins/events',
        component: () => import('./components/admin/pages/EventsIndex.vue'),
    },
];

// Create router
const router = createRouter({
    history: createWebHistory(),
    linkActiveClass: 'border-indigo-500',
    linkExactActiveClass: 'border-indigo-700',
    routes,
});

// Create and mount the app
createApp(App)
    .use(router)
    .mount('#app');
