<!-- App.vue -->
<template>
    <div class="w-full relative">
        <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-gray-100 bg-opacity-75 z-50">
            <div class="loader"></div>
        </div>
        <Navbar :user="user" @handleLogout="handleLogout"/>
        <div class="py-5">
            <router-view :me="user" @update="update"/>
        </div>

        <NotificationGroup v-for="type in notificationTypes" :key="type" :group="type" v-show="!loading">
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
                            <div class="flex items-center justify-center w-12" :class="bgColorClass[type]">
                                <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 40 40">
                                    <path :d="iconPath[type]"/>
                                </svg>
                            </div>
                            <div class="px-4 py-2 -mx-3">
                                <div class="mx-3">
                                    <span class="font-semibold" :class="textColorClass[type]">
                                        {{
                                            notification.title
                                        }}{{ type === 'generic' || type === 'success' ? 'Info' : '' }}
                                    </span>
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

<script setup>
import {onMounted, provide, ref, watch} from 'vue';
import axios from "axios";
import Navbar from '../Navbar.vue';
import {Notification, NotificationGroup, notify} from "notiwind";
import {useRouter as $router} from "vue-router";

// Initialiser window.Laravel hvis det ikke eksisterer
window.Laravel = window.Laravel || {
    permissions: [],
    roles: []
};

const user = ref(null);
const loading = ref(false);
const loaderClass = "loader"; // Define spinner style if needed
const emits = defineEmits(['update']);
const notificationTypes = ['error', 'generic', 'success'];

const bgColorClass = {
    error: 'bg-red-500',
    generic: 'bg-blue-500',
    success: 'bg-green-500'
};

const textColorClass = {
    error: 'text-red-500',
    generic: 'text-blue-500',
    success: 'text-blue-500'
};
const update = () => {
    fetchAuthenticatedUser();
}
const iconPath = {
    error: 'M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM21.6667 28.3333H18.3334V25H21.6667V28.3333ZM21.6667 21.6666H18.3334V11.6666H21.6667V21.6666Z',
    generic: 'M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM21.6667 28.3333H18.3334V25H21.6667V28.3333ZM21.6667 21.6666H18.3334V11.6666H21.6667V21.6666Z',
    success: 'M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM21.6667 28.3333H18.3334V25H21.6667V28.3333ZM21.6667 21.6666H18.3334V11.6666H21.6667V21.6666Z'
};

const fetchAuthenticatedUser = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/user');
        user.value = response.data;

        if (user.value) {
            localStorage.setItem('user', JSON.stringify(user.value));
            // Oppdater Laravel objekt med permissions og roles
            window.Laravel.permissions = response.data.permissions || [];
            window.Laravel.roles = response.data.roles || [];
        }
        return user.value;
    } catch (error) {
        if (error.response?.status === 401) {
            clearUserSession();
        } else {
            console.error('Error fetching user:', error);
        }
        return null;
    } finally {
        loading.value = false;
    }
};

const clearUserSession = () => {
    console.log('clear')
    user.value = null;
    localStorage.removeItem('user');
    localStorage.removeItem('token');
    delete axios.defaults.headers.common['Authorization'];
    window.Laravel.permissions = [];
    window.Laravel.roles = [];

    notify({
        group: "success",
        title: "Success",
        text: "Logged out successfully",
    });
};

const handleLogout = async () => {
    console.log('handle')
    try {
        await axios.post('/api/logout');

        clearUserSession();
        $router.push('/');
    } catch (error) {
        console.error('Logout failed:', error);
        notify({
            group: "error",
            title: "Error",
            text: "Failed to log out",
        });
    }
};

// Kjør ved oppstart
onMounted(async () => {
    loading.value = true;
    console.log('App mounted, checking authentication');
    const token = localStorage.getItem('token');
    if (token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        await fetchAuthenticatedUser();
    } else {
        loading.value = false;
    }
    loading.value = false;
});

// Provide nødvendige funksjoner
provide('fetchAuthenticatedUser', fetchAuthenticatedUser);

// Watch for user changes
watch(user, (newVal) => {
    console.log("User changed:", newVal);
});
</script>
