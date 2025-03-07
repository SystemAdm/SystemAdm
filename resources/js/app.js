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
    faBan,
    faBars,
    faBell,
    faBellConcierge,
    faBellSlash,
    faCalendarPlus,
    faCheck,
    faCheckCircle,
    faCircleNotch,
    faCirclePlus,
    faClock,
    faDoorClosed,
    faDoorOpen,
    faEnvelope,
    faEraser,
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
    faPencil,
    faPenToSquare,
    faPeoplePulling,
    faPhone,
    faPlayCircle,
    faPlus,
    faQrcode,
    faRandom,
    faRegistered,
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
import {routes} from "./routes.js";
import {createPinia} from 'pinia';
import {useUserStore} from "@/stores/userStore.js";

library.add(faUser, faMagnifyingGlass, faEnvelope, faPhone, faMapMarker, faShield, faQrcode, faIdCard, faBars, faTrashCan, faArrowRight, faArrowLeft, faLock, faClock, faCheck, faTrashCanArrowUp, faXmark, faPeoplePulling, faUserPlus, faEye, faEyeSlash, faRightToBracket, faRotateLeft, faInfoCircle, faPlus, faTriangleExclamation, faTrash, faPencil, faStar, faStamp, faRegularStar, faLink, faGoogle, faGithub, faCircleNotch, faPlayCircle, faStopCircle, faCirclePlus, faLockOpen, faSpinner, faSave, faRandom, faUserShield, faSignIn, faGear, faAddressCard, faFlagCheckered, faTimesCircle, faCheckCircle, faCalendarPlus, faDoorOpen, faSkullCrossbones, faIdCardClip, faDoorClosed, faRegistered, faBellConcierge, faPenToSquare, faEraser, faBan, faBellSlash, faBell);
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

const pinia = createPinia();

// Create router
const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Create and mount the app
(async () => {
    const pinia = createPinia();

    // Opprett Vue-instansen, men vent på rolleinitialisering
    const app = createApp(App)
        .component('font-awesome-icon', FontAwesomeIcon)
        .use(i18nVue, {
            lang: 'no',
            resolve: async lang => {
                const langs = import.meta.glob('../../lang/*.json');
                return await langs[`../../lang/${lang}.json`]();
            }
        })
        .use(pinia);

    const userStore = useUserStore();

    // Vent på at data lastes før resten av applikasjonen fortsetter
    console.log("Before hydrateUser");
    await userStore.hydrateUser();
    console.log("After hydrateUser");

    console.log("Before setting up plugin");
    app.use(router);
    app.use(LaravelPermissionToVueJS, {
        roles: () => window.Laravel.jsPermissions.roles,
        permissions: () => window.Laravel.jsPermissions.permissions,
    });

    console.log("jsPermissionToVueJS "+ window.Laravel.jsPermissions.roles);
    console.log("After setting up plugin");

    app.mount('#app');
})();

