// app.js
import {createApp} from 'vue';
import {createRouter, createWebHistory} from 'vue-router';
import App from './components/pages/App.vue';
import LaravelPermissionToVueJS from 'laravel-permission-to-vuejs'
import axios from "axios";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {library} from '@fortawesome/fontawesome-svg-core'
import {i18nVue} from 'laravel-vue-i18n'
import {
    faAddressCard,
    faArrowLeft,
    faArrowRight,
    faBars, faBellConcierge,
    faCalendarPlus,
    faCheck,
    faCheckCircle,
    faCircleNotch,
    faCirclePlus,
    faClock,
    faDoorClosed,
    faDoorOpen,
    faEnvelope, faEraser,
    faEye,
    faEyeSlash,
    faFlagCheckered,
    faGear,
    faIdCard,
    faIdCardClip,
    faInfoCircle,
    faLink,
    faLock,
    faLockOpen,
    faMagnifyingGlass,
    faMapMarker,
    faPencil, faPenToSquare,
    faPeoplePulling,
    faPhone,
    faPlayCircle,
    faPlus,
    faQrcode,
    faRandom, faRegistered,
    faRightToBracket,
    faRotateLeft,
    faSave,
    faShield,
    faSignIn,
    faSkullCrossbones,
    faSpinner,
    faStamp,
    faStar,
    faStopCircle,
    faTimesCircle,
    faTrash,
    faTrashCan,
    faTrashCanArrowUp,
    faTriangleExclamation,
    faUser,
    faUserPlus,
    faUserShield,
    faXmark,
} from '@fortawesome/free-solid-svg-icons'
import {faStar as faRegularStar} from '@fortawesome/free-regular-svg-icons';
import {faGithub, faGoogle} from "@fortawesome/free-brands-svg-icons";

library.add(faUser, faMagnifyingGlass, faEnvelope, faPhone, faMapMarker, faShield, faQrcode, faIdCard, faBars, faTrashCan, faArrowRight, faArrowLeft, faLock, faClock, faCheck, faTrashCanArrowUp, faXmark, faPeoplePulling, faUserPlus, faEye, faEyeSlash, faRightToBracket, faRotateLeft, faInfoCircle, faPlus, faTriangleExclamation, faTrash, faPencil, faStar, faStamp, faRegularStar, faLink, faGoogle, faGithub, faCircleNotch, faPlayCircle, faStopCircle, faCirclePlus, faLockOpen, faSpinner, faSave, faRandom, faUserShield, faSignIn, faGear, faAddressCard, faFlagCheckered, faTimesCircle, faCheckCircle, faCalendarPlus, faDoorOpen, faSkullCrossbones, faIdCardClip, faDoorClosed,faRegistered,faBellConcierge,faPenToSquare,faEraser);
axios.defaults.withCredentials = true;

// Define routes
const routes = [
    {
        path: '/signin',
        name: 'SignIn',
        component: () => import('./components/pages/Login.vue'), // Lazy load
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
];

// Create router
const router = createRouter({
    history: createWebHistory(),
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
    .use(LaravelPermissionToVueJS);

app.mount('#app');
