<!-- App.vue -->
<template>
    <div class="w-full">
        <Navbar :user="user" @logout="logout"/>
        <div class="py-5">
            <router-view :me="user"/> <!-- Displays routed components -->
        </div>

        <!-- Notification Groups (Error, Generic, Success) -->
        <!-- Error Notification Group -->
        <NotificationGroup group="error">
            <div class="fixed inset-0 flex items-start justify-end p-6 px-4 py-6 pointer-events-none">
                <div class="w-full max-w-sm">
                    <Notification
                        v-slot="{ notifications }"
                        enter="transform ease-out duration-300 transition"
                        enter-from="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
                        enter-to="translate-y-0 opacity-100 sm:translate-x-0"
                        leave="transition ease-in duration-500"
                        leave-from="opacity-100"
                        leave-to="opacity-0"
                        move="transition duration-500"
                        move-delay="delay-300"
                    >
                        <div
                            class="flex w-full max-w-sm mx-auto mt-4 overflow-hidden bg-white rounded-lg shadow-md"
                            v-for="notification in notifications"
                            :key="notification.id"
                        >
                            <div class="flex items-center justify-center w-12 bg-red-500">
                                <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 40 40">
                                    <path d="..."/>
                                </svg>
                            </div>
                            <div class="px-4 py-2 -mx-3">
                                <div class="mx-3">
                                    <span class="font-semibold text-red-500">{{ notification.title }}</span>
                                    <p class="text-sm text-gray-600">{{ notification.text }}</p>
                                </div>
                            </div>
                        </div>
                    </Notification>
                </div>
            </div>
        </NotificationGroup>

        <NotificationGroup group="generic">
            <div
                class="fixed inset-0 flex items-start justify-end p-6 px-4 py-6 pointer-events-none"
            >
                <div class="w-full max-w-sm">
                    <Notification
                        v-slot="{ notifications }"
                        enter="transform ease-out duration-300 transition"
                        enter-from="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
                        enter-to="translate-y-0 opacity-100 sm:translate-x-0"
                        leave="transition ease-in duration-500"
                        leave-from="opacity-100"
                        leave-to="opacity-0"
                        move="transition duration-500"
                        move-delay="delay-300"
                    >
                        <div
                            class="flex w-full max-w-sm mx-auto mt-4 overflow-hidden bg-white rounded-lg shadow-md"
                            v-for="notification in notifications"
                            :key="notification.id"
                        >
                            <div class="flex items-center justify-center w-12 bg-blue-500">
                                <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 40 40"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM21.6667 28.3333H18.3334V25H21.6667V28.3333ZM21.6667 21.6666H18.3334V11.6666H21.6667V21.6666Z"/>
                                </svg>
                            </div>

                            <div class="px-4 py-2 -mx-3">
                                <div class="mx-3">
                                    <span class="font-semibold text-blue-500">{{ notification.title }}Info</span>
                                    <p class="text-sm text-gray-600">{{ notification.text }}</p>
                                </div>
                            </div>
                        </div>
                    </Notification>
                </div>
            </div>
        </NotificationGroup>

        <NotificationGroup group="success">
            <div
                class="fixed inset-0 flex items-start justify-end p-6 px-4 py-6 pointer-events-none"
            >
                <div class="w-full max-w-sm">
                    <Notification
                        v-slot="{ notifications }"
                        enter="transform ease-out duration-300 transition"
                        enter-from="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
                        enter-to="translate-y-0 opacity-100 sm:translate-x-0"
                        leave="transition ease-in duration-500"
                        leave-from="opacity-100"
                        leave-to="opacity-0"
                        move="transition duration-500"
                        move-delay="delay-300"
                    >
                        <div
                            class="flex w-full max-w-sm mx-auto mt-4 overflow-hidden bg-white rounded-lg shadow-md"
                            v-for="notification in notifications"
                            :key="notification.id"
                        >
                            <div class="flex items-center justify-center w-12 bg-green-500">
                                <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 40 40"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM21.6667 28.3333H18.3334V25H21.6667V28.3333ZM21.6667 21.6666H18.3334V11.6666H21.6667V21.6666Z"/>
                                </svg>
                            </div>
                            <div class="px-4 py-2 -mx-3">
                                <div class="mx-3">
                                    <span class="font-semibold text-blue-500">{{ notification.title }}Info</span>
                                    <p class="text-sm text-gray-600">{{ notification.text }}</p>
                                </div>
                            </div>
                        </div>
                    </Notification>
                </div>
            </div>
        </NotificationGroup>
    </div>
</template>

<script>
import axios from "axios";
import Navbar from '../Navbar.vue';
import {Notification, NotificationGroup} from "notiwind";

export default {
    setup() {
        console.log(localStorage.getItem('user'))
    },
    components: {
        Notification,
        NotificationGroup,
        Navbar,
    },
    data() {
        return {
            user: null,
            loading: false, // Optional loading indicator
        };
    },
    async created() {
        if (!this.user) {
            await this.fetchAuthenticatedUser();
        }
        // Initialize user data on app load
    },
    methods: {
        async fetchAuthenticatedUser() {
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) {
                try {
                    // Optionally, check with the backend to verify the user is still authenticated
                    const response = await axios.get('/api/user');

                    // If user data is valid, set it to the state
                    this.user = response.data;
                } catch (error) {
                    // If the user is not authenticated, clear the local storage and set user to null
                    console.error('User session expired or not authenticated', error);
                    localStorage.removeItem('user');
                    this.user = null;
                }
            } else {
                // If no user is found in local storage, fetch from backend
                try {
                    const response = await axios.get('/api/user');
                    this.user = response.data;
                    // Save fresh user data to local storage
                    localStorage.setItem('user', (this.user) ? JSON.stringify(this.user) : null);
                } catch (error) {
                    //console.error('User is not authenticated', error);
                    this.user = null;
                }
            }
        },
        clearUserSession() {
            this.user = null;
            localStorage.removeItem('user');
            this.$notify({
                group: "success",
                title: "Success",
                text: "Logged out successfully",
            });
        },
        logout() {
            axios.post('/api/logout')
                .then(() => {
                    this.clearUserSession();
                })
                .catch(error => {
                    console.log('Logout failed', error);
                    this.$notify({
                        group: "error",
                        title: "Error",
                        text: "Failed to log out",
                    });
                });
        }
    },
    watch: {
        user(newVal) {
            console.log("User changed:", newVal);
        },
    },
    provide() {
        return {
            user: this.user, // Provide user to all child components
        };
    },
};
</script>
