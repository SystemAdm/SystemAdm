<template>
    Dashboard {{ is('Administrator')?'true':'false' }}
    <div>
        <button @click="logout">Logout</button>
    </div>
</template>
<script setup>
import { onMounted } from 'vue'
import { useUserStore } from '@/stores/userStore' // Pinia-store
import {useRouter} from "vue-router";
import axios from "axios";
import { is, can } from 'laravel-permission-to-vuejs'

const userStore = useUserStore() // Får tilgang til Pinia-store
const router = useRouter()
async function logout() {
    const userStore = useUserStore();
    const router = useRouter(); // For navigasjon

    // Fjern brukerdata lokalt
    userStore.clearUser();

    try {
        // Send API-forespørsel til backend for å logge ut
        await axios.post('/api/logout');
        console.log('User logged out successfully');
    } catch (error) {
        console.error('Failed to logout from API:', error.message || error);
    } finally {
        // Naviger brukeren til innloggingssiden, uansett
        await router.push({ name: 'SignIn' });
    }
}



onMounted(() => {
    console.log(userStore) // Skal vise Pinia-store-objektet
    console.log(userStore.user.full_name) // Tilgang til state
    console.log(userStore.roles);
// Output: ['Administrator', 'Editor', 'User']
    console.log(is('Administrator')); // True eller False?
})
</script>

