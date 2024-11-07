<template>
    <nav class="relative px-4 py-4 flex justify-between items-center bg-dark">
        <a href="#" class="text-3xl font-bold leading-none">Spillhuset</a>
        <div class="lg:hidden">
            <button class="navbar-burger flex items-center text-blue-600 p-3" @click="toggleMenu">
                LOGO
            </button>
        </div>
        <ul class="hidden absolute top-1/2 left-1/2 transform -translate-y-1/2 -translate-x-1/2 lg:flex lg:mx-auto lg:items-center lg:w-auto lg:space-x-6"
            :class="{ 'hidden': this.isMenuOpen }">
            <li><router-link active-class="text-blue-600 hover:text-blue-600 text-bold" class="font-bold" to="/">Hovedside</router-link></li>

            <li><router-link active-class="text-blue-600 hover:text-blue-600 text-bolder" class="font-bold" to="/events">Arrangementer</router-link></li>
        </ul>
        <div v-if="user == null"><a class="hidden lg:inline-block lg:ml-auto lg:mr-3 py-2 px-6 bg-gray-50 hover:bg-gray-100 text-sm text-gray-900 font-bold rounded-xl transition duration-200"
           href="#">Sign In</a>
        <router-link class="hidden lg:inline-block py-2 px-6 bg-blue-500 hover:bg-blue-600 text-sm text-white font-bold rounded-xl transition duration-200"
           to="/signup">Sign Up</router-link>
        </div>
        <div v-else><a class="hidden lg:inline-block lg:ml-auto lg:mr-3 py-2 px-6 bg-gray-50 hover:bg-gray-100 text-sm text-gray-900 font-bold rounded-xl transition duration-200"
                       @click="logout">Sign Out</a></div>
    </nav>
    <div class="navbar-menu relative z-50 bg-black" :class="{ 'hidden': !this.isMenuOpen }">
        <div class="navbar-backdrop fixed inset-0 bg-gray-800 " @click="toggleMenu"></div>
        <nav
            class="fixed top-0 left-0 bottom-0 flex flex-col w-5/6 max-w-sm py-6 px-6 bg-dark border-r overflow-y-auto">
            <div class="flex items-center mb-8">
                <a class="mr-auto text-3xl font-bold leading-none" href="#">

                </a>
                <button class="navbar-close" @click="toggleMenu">

                </button>
            </div>
            <!-- Mobile menu items go here -->
            <ul>
                <li class="mb-1">
                    <a class="block p-4 text-sm font-semibold text-gray-400 hover:bg-blue-50 hover:text-blue-600 rounded"
                       href="#">Home</a>
                </li>
                <li class="mb-1">
                    <a class="block p-4 text-sm font-semibold text-gray-400 hover:bg-blue-50 hover:text-blue-600 rounded"
                       href="#">About Us</a>
                </li>
                <li class="mb-1">
                    <a class="block p-4 text-sm font-semibold text-gray-400 hover:bg-blue-50 hover:text-blue-600 rounded"
                       href="#">Services</a>
                </li>
                <li class="mb-1">
                    <a class="block p-4 text-sm font-semibold text-gray-400 hover:bg-blue-50 hover:text-blue-600 rounded"
                       href="#">Pricing</a>
                </li>
                <li class="mb-1">
                    <a class="block p-4 text-sm font-semibold text-gray-400 hover:bg-blue-50 hover:text-blue-600 rounded"
                       href="#">Contact</a>
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
        };
    },
    methods: {
        toggleMenu() {
            this.isMenuOpen = !this.isMenuOpen;
        },
        logout(){
            this.$emit('logout');
        }
    },
};
</script>
