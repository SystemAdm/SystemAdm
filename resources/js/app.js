// app.js
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './components/App.vue';
import Home from './components/Home.vue';
import NextEvent from "./components/NextEvent.vue"; // Make sure this path is correct

// Define routes
const routes = [
    { path: '/', component: Home }, // Home should be loaded on the root route
    { path: '/test', component: NextEvent },
    { path: '/admins/events', component: () => import('./components/AdminEventsIndex.vue'), meta: { title: 'Events' } },
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
