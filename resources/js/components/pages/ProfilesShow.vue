<template>
    <div v-if="loading === false && user != null" class="flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-4xl w-full p-8 transition-all duration-300 animate-fade-in">
            <div class="flex flex-col md:flex-row">
                <div class="md:w-1/3 text-center mb-8 md:mb-0">
                    <img :src="user.profile.image" alt="Profile Picture"
                         class="rounded-full w-48 h-48 mx-auto mb-4 border-4 border-indigo-800 dark:border-blue-900 transition-transform duration-300 hover:scale-105">
                    <h1 class="text-2xl font-bold text-indigo-800 dark:text-white mb-2">{{ user.fullname }}</h1>

                    <div>
                        <abbr v-show="user.profile.police_report != null" title="Police report, checked ok"><font-awesome-icon :icon="['fas', 'shield']" class="mr-3" /></abbr>
                        <div v-if="user.rank != null" class="inline-flex">
                            <span v-if="user.rank.name ==='Administrator'"
                                  class="font-bold text-red-500">Administrator</span>
                            <span v-else-if="user.rank.name ==='Styret'" class="font-bold text-green-500">Styret</span>
                            <span v-else-if="user.rank.name ==='Crew'"
                                  class="font-bold text-yellow-500">Crew</span>
                            <span v-else-if="user.rank.name ==='Member'" class="font-bold text-blue-500">Member</span>
                        </div>

                    </div>
                    <button v-if="me != null && user.id === me.id"
                            class="mt-4 bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition-colors duration-300">
                        Edit Profile
                    </button>
                </div>
                <div class="md:w-2/3 md:pl-8">
                    <h2 class="text-xl font-semibold text-indigo-800 dark:text-white mb-4">About Me</h2>
                    <p class="text-gray-700 dark:text-gray-300 mb-6">
                        Passionate software developer with 5 years of experience in web technologies.
                        I love creating user-friendly applications and solving complex problems.
                    </p>
                    <h2 class="text-xl font-semibold text-indigo-800 dark:text-white mb-4">Roles</h2>
                    <div class="flex flex-wrap gap-2 mb-6">
                        <div v-for="role in user.roles">
                            <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full font-bold">{{
                                    role.name
                                }}</span>
                        </div>
                    </div>
                    <h2 class="text-xl font-semibold text-indigo-800 dark:text-white mb-4">Contact Information</h2>
                    <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                        <li class="flex items-center"
                            v-if="true ||(user.profile.show_email && user.primary_email != null)">
                            <font-awesome-icon :icon="['fas','fa-envelope']"></font-awesome-icon>
                            <span class="pl-2">test{{ user.primary_email }}</span>
                        </li>
                        <li class="flex items-center">
                            <font-awesome-icon :icon="['fas','fa-phone']"></font-awesome-icon>
                            <span class="pl-2">test{{ user.primary_email }}</span>
                        </li>
                        <li class="flex items-center">
                            <font-awesome-icon :icon="['fas','fa-map-marker']"></font-awesome-icon>
                            <span class="pl-2">{{ user.profile.postal }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from "axios";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

export default {
    name: 'Profile',
    components: {FontAwesomeIcon},
    props: {
        me: Object,
    },
    data() {
        return {user: null, loading: true}
    },
    methods: {
        fetch() {
            axios.get('/api/users/' + this.$route.params.user).then(response => {
                this.user = response.data;
                this.loading = false;
            });
        }
    }, mounted() {
        this.fetch();
    }
}
</script>
