<template>
    <nav class="relative px-4 py-4 bg-dark">
        <div class="flex justify-between">
        <router-link to="/" class="text-3xl font-bold leading-none">
            <img src="/images/logos/spillhuset-logo-white.png" alt="" class="h-24 w-auto">
        </router-link>

        <div class="lg:hidden">
            <button class="navbar-burger  text-blue-600 p-3" @click="toggleMenu">
                <font-awesome-icon :icon="['fas', 'bars']" />
            </button>
        </div>
        </div>
        <ul class="hidden absolute top-1/2 left-1/2 transform -translate-y-1/2 -translate-x-1/2 lg:flex lg:mx-auto lg:items-center lg:w-auto lg:space-x-6"
            :class="{ 'hidden': this.isMenuOpen }">
            <li v-for="link in menu">
                <router-link active-class="text-blue-600 hover:text-blue-600 text-bold" class="font-bold"
                             :to="link.url">
                    {{ link.name }}
                </router-link>
            </li>
        </ul>
        <div v-if="user == null"><a
            class="hidden lg:inline-block lg:ml-auto lg:mr-3 py-2 px-6 bg-gray-50 hover:bg-gray-100 text-sm text-gray-900 font-bold rounded-xl transition duration-200"
            href="#">Sign In</a>
            <router-link
                class="hidden lg:inline-block py-2 px-6 bg-blue-500 hover:bg-blue-600 text-sm text-white font-bold rounded-xl transition duration-200"
                to="/signup">Sign Up
            </router-link>
        </div>
        <div v-else><a
            class="hidden lg:inline-block lg:ml-auto lg:mr-3 py-2 px-6 bg-gray-50 hover:bg-gray-100 text-sm text-gray-900 font-bold rounded-xl transition duration-200"
            @click="logout">Sign Out</a></div>
    </nav>
    <div class="navbar-menu relative z-50 bg-black" :class="{ 'hidden': !this.isMenuOpen }">
        <div class="navbar-backdrop fixed inset-0 bg-black" @click="toggleMenu"></div>
        <nav
            class="fixed top-0 left-0 bottom-0 flex flex-col w-4/6 max-w-sm py-6 px-6 bg-black border-r overflow-y-auto text-center">
            <button class="navbar-close text-red-700 text-xl" @click="toggleMenu">
                &times;
            </button>
            <div class="place-items-center mb-8">
                <router-link @click="toggleMenu" to="/" class="text-3xl font-bold leading-none"><img src="/images/logos/spillhuset-logo-white.png"
                                                                                 alt="" class="h-24 w-auto"></router-link>
            </div>
            <!-- Mobile menu items go here -->
            <div v-if="user == null" class="mb-5">
                <router-link @click="toggleMenu"
                    class="py-2 px-6 bg-blue-500 hover:bg-blue-600 text-sm text-white font-bold rounded-xl transition duration-200"
                    to="/signup">Sign Up
                </router-link>
            </div>
            <div v-else><a
                class=" py-2 px-6 bg-gray-50 hover:bg-gray-100 text-sm text-gray-900 font-bold rounded-xl transition duration-200"
                @click="toggleMenu; logout">Sign Out</a></div>
            <ul>
                <li v-for="link in menu" class="mb-1">
                    <router-link active-class="text-blue-600 hover:text-blue-600 text-bold" class="font-bold"
                                 :to="link.url">
                        {{ link.name }}
                    </router-link>
                </li>
            </ul>


        </nav>
    </div>
</template>

<script>
export default {
    emits: ['logout'],
    props: {
        user: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            isMenuOpen: false,
            menu: {
                main: {
                    name: 'Index',
                    url: '/'
                },
                events: {
                    name: 'Events',
                    url: '/events'
                }
            },
            auth: {
                login:{
                    name: 'Login/Register',
                    url:'/signup'
                },
            }
        }
    },
    methods: {
        toggleMenu() {
            this.isMenuOpen = !this.isMenuOpen;
        },
        logout() {
            this.$emit('logout');
        }
    },
};
</script>
