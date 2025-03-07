<template>
    <nav class="bg-white dark:bg-black border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="/" class="text-xl font-bold text-black dark:text-white">
                        <img
                            v-if="!isDarkMode"
                            src="/images/logos/spillhuset-logo-black.png"
                            :alt="appName"
                            class="h-12">
                        <img
                            v-else
                            src="/images/logos/spillhuset-logo-white.png"
                            :alt="appName"
                            class="h-12">
                    </a>
                </div>

                <!-- Hamburger Menu (visible on mobile) -->
                <div class="flex md:hidden">
                    <button
                        @click="toggleMobileMenu"
                        type="button"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-black dark:hover:text-white focus:outline-none"
                        aria-expanded="false"
                    >
                        <span class="sr-only">Open main menu</span>

                        <font-awesome-icon  :icon="['fas', (isMobileMenuOpen)?'xmark':'bars']" class="block h-6 w-6"/>
                    </button>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-4">
                    <router-link to="/signin">{{ trans('auth.login') }}</router-link>

                    <button
                        @click="toggleDarkMode"
                        class="text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-white"
                    >
                        <span v-if="isDarkMode">☀️</span>
                        <span v-else>🌙</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div
            class="md:hidden"
            v-if="isMobileMenuOpen"
        >
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <button
                    @click="signIn"
                    class="block w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none"
                >
                    Sign In
                </button>

                <button
                    @click="toggleDarkMode"
                    class="block w-full text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-white border-gray-500 px-4 py-2 rounded-md"
                >
                    <span v-if="isDarkMode">☀️</span>
                    <span v-else>🌙</span>
                </button>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref, onMounted } from "vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {trans} from "laravel-vue-i18n";

const isDarkMode = ref(false);
const isMobileMenuOpen = ref(false);

const appName = import.meta.env.VITE_APP_NAME || 'Spillhuset';

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    if (isDarkMode.value) {
        document.documentElement.classList.add("dark");
        localStorage.setItem("theme", "dark");
    } else {
        document.documentElement.classList.remove("dark");
        localStorage.setItem("theme", "light");
    }
};

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

onMounted(() => {
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark") {
        isDarkMode.value = true;
        document.documentElement.classList.add("dark");
    }
});
</script>

<style scoped>
/* Placeholder for custom styles (optional) */
</style>
