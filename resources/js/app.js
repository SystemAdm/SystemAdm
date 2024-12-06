// app.js
import {createApp} from 'vue';
import {createRouter, createWebHistory} from 'vue-router';
import App from './components/pages/App.vue';
import Notifications from 'notiwind'
import LaravelPermissionToVueJS from 'laravel-permission-to-vuejs'
import axios from "axios";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {library} from '@fortawesome/fontawesome-svg-core'
import {i18nVue} from 'laravel-vue-i18n'
import {
    faArrowLeft,
    faArrowRight,
    faBars,
    faCheck,
    faClock,
    faEnvelope,
    faEye,
    faEyeSlash,
    faIdCard,
    faInfoCircle, faLink,
    faLock,
    faMagnifyingGlass,
    faMapMarker, faPencil,
    faPeoplePulling,
    faPhone,
    faPlus,
    faQrcode,
    faRightToBracket,
    faRotateLeft,
    faShield, faStamp, faStar, faTrash,
    faTrashCan,
    faTrashCanArrowUp,
    faTriangleExclamation,
    faUser,
    faUserPlus,
    faXmark,
} from '@fortawesome/free-solid-svg-icons'
import { faStar as faRegularStar } from '@fortawesome/free-regular-svg-icons';

library.add(faUser, faMagnifyingGlass, faEnvelope, faPhone, faMapMarker, faShield, faQrcode, faIdCard, faBars, faTrashCan, faArrowRight, faArrowLeft, faLock, faClock, faCheck, faTrashCanArrowUp, faXmark, faPeoplePulling, faUserPlus, faEye, faEyeSlash, faRightToBracket, faRotateLeft, faInfoCircle, faPlus, faTriangleExclamation,faTrash,faPencil,faStar,faStamp,faRegularStar,faLink);
axios.defaults.withCredentials = true;

// Define routes
const routes = [
    {name: 'Dashboard', path: '/dashboard', component: () => import('./components/pages/Dashboard.vue')},
    {name: 'AdminRules', path: '/admin/rules', component: () => import('./components/admin/pages/RulesIndex.vue')},
    {name: 'RulesIndex', path: '/rules', component: () => import('./components/pages/RulesIndex.vue')},
    {name: 'Membership', path: '/member', component: () => import('./components/pages/Membership.vue')},
    {name: 'Index', path: '/', component: () => import('./components/pages/Home.vue')}, // Home should be loaded on the root route
    {name: 'EventsIndex', path: '/events', component: () => import('./components/pages/EventsIndex.vue')},
    {name: 'WhoAmI?', path: '/login', component: () => import( './components/pages/Signup.vue')},
    {name: 'MyProfile', path: '/profiles/me', component: () => import('./components/pages/MyProfile.vue')},
    {name: 'ProfilesIndex', path: '/profiles', component: () => import('./components/pages/ProfilesIndex.vue')},
    {name: 'ProfilesShow', path: '/profiles/:user', component: () => import('./components/pages/ProfilesShow.vue')},
    {
        name: 'AdminEvents',
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
const app = createApp(App)
    .component('font-awesome-icon', FontAwesomeIcon)
    .use(i18nVue, {
        lang: 'no',
        resolve: async lang => {
            const langs = import.meta.glob('../../lang/*.json');
            return await langs[`../../lang/${lang}.json`]();
        }
    })
    .use(router)
    .use(Notifications)
    .use(LaravelPermissionToVueJS);

app.mount('#app');
