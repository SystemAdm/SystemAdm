<template>
    <div class="flex items-center justify-center min-h-screen bg-black">
        <div class="w-full max-w-md p-6 bg-gray-900 rounded-lg shadow-lg">
            <!-- Logo -->
            <div class="flex flex-col items-center mb-6">
                <template v-if="logoEnabled">
                    <img
                        src="/images/logos/spillhuset-logo-white.png"
                        alt="APP_NAME"
                        class="h-48 mb-3"
                    />
                </template>
                <template v-else>
                    <h1 class="text-3xl font-bold text-white tracking-wide">{{ appName }}</h1>
                </template>
            </div>

            <!-- Tittel -->
            <h2 class="mb-6 text-2xl font-semibold text-center text-white">Logg Inn</h2>

            <!-- Skjema - Logg inn -->
            <form @submit.prevent="handleSignIn">
                <div class="mb-4">
                    <label for="identifier" class="block text-sm font-medium text-gray-300">
                        E-post, Telefon eller Brukernavn
                    </label>
                    <input
                        id="identifier"
                        v-model="form.identifier"
                        type="text"
                        required
                        placeholder="F.eks. epost@domene.no, +4712345678 eller brukernavn"
                        class="w-full px-4 py-2 mt-2 text-white bg-gray-800 border border-gray-600 rounded-md placeholder-gray-400 focus:ring-2 focus:ring-blue-600 focus:outline-none"
                    />
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-300">Passord</label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        placeholder="Ditt passord"
                        class="w-full px-4 py-2 mt-2 text-white bg-gray-800 border border-gray-600 rounded-md placeholder-gray-400 focus:ring-2 focus:ring-blue-600 focus:outline-none"
                    />
                </div>
                <button
                    type="submit"
                    class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400"
                >
                    Logg Inn
                </button>
            </form>

            <!-- Glemt passord-lenke -->
            <div class="mt-4 text-center">
                <router-link
                    to="/forgot-password"
                    class="text-sm font-medium text-blue-400 hover:underline"
                >
                    Glemt passord?
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref} from 'vue';
import axios from 'axios';
import {useRouter as $router} from 'vue-router';
import {notify} from 'notiwind'; // Varsler

const appName = import.meta.env.VITE_APP_NAME || 'Appnavn'; // Hent navn fra .env eller standard
const logoEnabled = true;

const form = ref({
    identifier: '', // Kan være e-post, telefonnummer eller brukernavn
    password: '',
});

const handleSignIn = async () => {
    try {
        const {data} = await axios.post('/api/login', form.value); // Send innloggingsdata til backend
        if (data.token) {
            localStorage.setItem('token', data.token); // Lagre token lokalt
            axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`;
            notify({
                group: 'success',
                title: 'Velkommen tilbake!',
                text: 'Logget inn med suksess.',
            });
            $router.push('/'); // Naviger til hovedsiden
        }
    } catch (error) {
        notify({
            group: 'error',
            title: 'Innlogging feilet',
            text: 'Kontroller opplysningene og prøv igjen.',
        });
    }
};
</script>
