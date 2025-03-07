<!-- App.vue -->
<template>
    <Navbar/>
    <router-view/>
</template>

<script setup>
import Navbar from '../Navbar.vue';
import axios from "axios";
import {useUserStore} from "@/stores/userStore.js";
import {onMounted, provide, ref} from "vue";

// Initialiser window.Laravel hvis det ikke eksisterer
window.Laravel = window.Laravel || {
    permissions: [],
    roles: []
};

const userStore = useUserStore();


onMounted(() => {
    userStore.hydrateUser();
});


</script>
