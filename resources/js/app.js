// app.js
import {createApp} from 'vue';
import {createRouter, createWebHistory} from 'vue-router';
import App from './components/pages/App.vue';

// Define routes
const routes = [
    {path: '/', component: () => import('./components/pages/Home.vue')}, // Home should be loaded on the root route

    {path: '/signup', component: () => import( './components/pages/Signup.vue')},
    {path: '/admins/events', component: () => import('./components/admin/pages/EventsIndex.vue'),},
];

// Create router
const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Create and mount the app
createApp(App)
    .use(router)
    .mount('#app');
