<template>
    <div v-show="loading === false" class="columns-3">
        <div v-for="user in users" :key="user.id">
            <section class="flex flex-col justify-center antialiased text-gray-600 min-h-screen p-4">
                <div class="h-full">
                    <!-- Card -->
                    <div class="max-w-sm mx-auto bg-white shadow-lg rounded-sm border border-gray-200">
                        <div class="flex flex-col h-full">
                            <!-- Card top -->
                            <div class="flex-grow p-5">
                                <div class="flex justify-between items-start">
                                    <!-- Image + name -->
                                    <header>
                                        <div class="flex mb-2">
                                            <div class="relative inline-flex items-start mr-5">
                                                <img class="rounded-full" :src="user.profile.image" width="64"
                                                     height="64" :alt="'Profile image of '+user.fullname"/>
                                            </div>
                                            <div class="mt-1 pr-1">
                                                <div class="inline-flex text-gray-800 hover:text-gray-900">
                                                    <h2 class="text-xl leading-snug justify-center font-semibold">
                                                        {{ user.fullname }}</h2>
                                                </div>
                                                <div class="flex items-center">
                                                    <div v-if="user.rank != null">
                                                    <span v-if="user.rank.name ==='Administrator'" class="font-bold text-red-500">Administrator</span>
                                                    <span v-else-if="user.rank.name ==='Styret'" class="font-bold text-green-500">Styret</span>
                                                    <span v-else-if="user.rank.name ==='Crew'"
                                                          class="font-bold text-yellow-500">Crew</span>
                                                    <span v-else-if="user.rank.name ==='Member'" class="font-bold text-blue-500">Member</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </header>
                                </div>
                            </div>
                            <!-- Card footer -->
                            <div class="border-t border-gray-200">
                                <div class="flex divide-x divide-gray-200r">
                                    <div
                                        class="block flex-1 text-center text-sm text-indigo-500 hover:text-indigo-600 font-medium px-1 py-2">
                                        <div v-if="me != null && user.id === me.id"></div>
                                        <div v-else class="flex justify-evenly"><img :src="user.profile.pegi" alt="" class="max-h-8"><img
                                            :src="user.profile.esrb" alt="" class="max-h-8">
                                        </div>
                                    </div>
                                    <router-link
                                        class="block flex-1 text-center text-sm text-gray-600 hover:text-gray-800 font-medium px-3 py-4 group"
                                        :to="'/profiles/'+user.id">
                                        <div class="flex items-center justify-center">
                                            <font-awesome-icon :icon="['fas', 'magnifying-glass']" />&nbsp;&nbsp;<span>View Profile</span>
                                        </div>
                                    </router-link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</template>
<script>
import axios from "axios";
import {is} from "laravel-permission-to-vuejs";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";


export default {
    name: 'ProfileIndex',
    components: {FontAwesomeIcon},
    props:{
        me: Object,
    },
    data() {
        return {users: null, loading: true}
    },
    methods:{
        is,
        fetch() {
            axios.get('/api/users').then(response => {
                this.users = response.data;
                this.loading = false;
            });
        }
    }, mounted() {
        this.fetch();
    }
}
</script>
