<template>
    <nav class="relative px-4 py-4 bg-dark">
        <div class="flex justify-between">
            <router-link to="/" class="text-3xl font-bold leading-none">
                <img src="/images/logos/spillhuset-logo-white.png" alt="" class="h-24 w-auto">
            </router-link>

            <div class="lg:hidden">
                <button class="navbar-burger text-blue-600 p-3" @click="toggleMenu">
                    <font-awesome-icon :icon="['fas', 'bars']" />
                </button>
            </div>
        </div>

        <ul class="hidden absolute top-1/2 left-1/2 transform -translate-y-1/2 -translate-x-1/2 lg:flex lg:mx-auto lg:items-center lg:w-auto lg:space-x-6"
            :class="{ 'hidden': isMenuOpen }">
            <li v-for="(item, key) in menu" :key="key">
                <router-link
                    active-class="text-blue-600 hover:text-blue-600 text-bold"
                    class="font-bold text-white hover:text-blue-600"
                    :to="item.url"
                >
                    {{ item.name }}
                </router-link>
            </li>
        </ul>

        <div v-if="!user" class="hidden lg:flex lg:items-center lg:space-x-3">
            <router-link
                class="py-2 px-6 bg-gray-50 hover:bg-gray-100 text-sm text-gray-900 font-bold rounded-xl transition duration-200"
                to="/login">{{ t('auth.sign_in') }}
            </router-link>
        </div>

        <div v-else class="hidden lg:flex lg:items-center">
            <span class="text-white mr-4">
                {{ user.given_name }} {{ user.family_name }}
            </span>
            <button
                class="py-2 px-6 bg-gray-50 hover:bg-gray-100 text-sm text-gray-900 font-bold rounded-xl transition duration-200"
                @click="handleLogoutClick">
                {{ t('auth.sign_out') }}
            </button>
        </div>

        <!-- Admin badge - bruk v-if med can metode -->
        <div v-if="user && can('access_admin')"
             class="hidden lg:inline-block px-2 py-1 bg-red-500 text-white text-xs font-bold rounded ml-2">
            Admin
        </div>

        <!-- Moderator badge -->
        <div v-else-if="user && can('moderate_events')"
             class="hidden lg:inline-block px-2 py-1 bg-blue-500 text-white text-xs font-bold rounded ml-2">
            Moderator
        </div>
    </nav>

    <div class="navbar-menu relative z-50 bg-black" :class="{ 'hidden': !isMenuOpen }">
        <div class="navbar-backdrop fixed inset-0 bg-black" @click="toggleMenu"></div>
        <nav class="fixed top-0 left-0 bottom-0 flex flex-col w-4/6 max-w-sm py-6 px-6 bg-black border-r overflow-y-auto text-center">
            <button class="navbar-close text-red-700 text-xl" @click="toggleMenu">
                &times;
            </button>
            <div class="place-items-center mb-8">
                <router-link @click="toggleMenu" to="/" class="text-3xl font-bold leading-none">
                    <img src="/images/logos/spillhuset-logo-white.png" alt="" class="h-24 w-auto">
                </router-link>
            </div>

            <div v-if="!user" class="mb-5">
                <router-link
                    @click="toggleMenu"
                    class="block mb-3 py-2 px-6 bg-gray-50 hover:bg-gray-100 text-sm text-gray-900 font-bold rounded-xl transition duration-200"
                    to="/login">{{ t('auth.sign_in') }}
                </router-link>
            </div>

            <div v-else class="mb-5">
                <span class="block text-white mb-3">
                    {{ user.given_name }} {{ user.family_name }}
                </span>
                <button
                    class="block w-full py-2 px-6 bg-gray-50 hover:bg-gray-100 text-sm text-gray-900 font-bold rounded-xl transition duration-200"
                    @click="handleLogoutClick">
                    {{ t('auth.sign_out') }}
                </button>
            </div>

            <ul>
                <li v-for="(item, key) in menu" :key="key" class="mb-1">
                    <router-link
                        @click="toggleMenu"
                        active-class="text-blue-600 hover:text-blue-600 text-bold"
                        class="font-bold text-white hover:text-blue-600"
                        :to="item.url"
                    >
                        {{ item.name }}
                    </router-link>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { notify } from './utils/notify';
import { trans } from 'laravel-vue-i18n';
const t = trans;
const props = defineProps({
    user: {
        type: Object,
        default: null,
    }
});

// Definer emits
const emit = defineEmits(['testClick']);

const isMenuOpen = ref(false);

const menu = computed(() => {
    if (!props.user) return {
        main: {
            name: trans('nav.home'),
            url: '/'
        },
        events: {
            name: trans('nav.events'),
            url: '/events'
        }
    };

    // Grunnmeny for innloggede brukere
    const baseMenu = {
        main: {
            name: trans('nav.home'),
            url: '/'
        },
        events: {
            name: trans('nav.events'),
            url: '/events'
        },
        profile: {
            name: trans('nav.profile'),
            url: '/profiles/me'
        }
    };

    // Admin-seksjoner
    if (window.Laravel?.permissions?.includes('access_admin')) {
        baseMenu.adminEvents = {
            name: trans('nav.admin_events'),
            url: '/admins/events'
        };
        baseMenu.adminRules = {
            name: trans('nav.admin_rules'),
            url: '/admins/rules'
        };
    }

    return baseMenu;
});

const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};

const handleLogoutClick = () => {
    emit('testClick');
    toggleMenu();
};

// Hjelpefunksjoner for tillatelser
const can = (permission) => {
    return window.Laravel?.permissions?.includes(permission) || false;
};

const hasRole = (role) => {
    return window.Laravel?.roles?.includes(role) || false;
};
</script>
