// app.js
import {createApp} from 'vue';
import {createRouter, createWebHistory} from 'vue-router';
import App from './components/pages/App.vue';
import Notifications from 'notiwind'
import LaravelPermissionToVueJS from 'laravel-permission-to-vuejs'
import axios from "axios";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import { library } from '@fortawesome/fontawesome-svg-core'
import {
    faEnvelope, faIdCard,
    faMagnifyingGlass,
    faMapLocation,
    faMapMarker,
    faPhone, faQrcode, faShield,
    faUser
} from '@fortawesome/free-solid-svg-icons'

library.add(faUser,faMagnifyingGlass,faEnvelope,faPhone,faMapMarker,faShield,faQrcode,faIdCard);
axios.defaults.withCredentials = true;

// Define routes
const routes = [
    {name: 'Membership', path: '/member', component: () => import('./components/pages/Membership.vue')},
    {name: 'Index', path: '/', component: () => import('./components/pages/Home.vue')}, // Home should be loaded on the root route
    {name: 'Events', path: '/events', component: () => import('./components/pages/Events.vue')},
    {name: 'Signup', path: '/signup', component: () => import( './components/pages/Signup.vue')},
    {name: 'MyProfile', path: '/profiles/me', component: () => import('./components/pages/MyProfile.vue')},
    {name: 'ProfilesIndex', path: '/profiles', component: () => import('./components/pages/ProfilesIndex.vue')},
    {name: 'ProfilesShow', path: '/profiles/:user', component: () => import('./components/pages/ProfilesShow.vue')},
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
    .component('font-awesome-icon', FontAwesomeIcon)
    .use(router)
    .use(Notifications)
    .use(LaravelPermissionToVueJS)
    .mount('#app');
