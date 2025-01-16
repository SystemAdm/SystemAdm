<template>
        <!-- Passordfelt -->
        <div class="mb-4 mt-4">
            <label for="password" class="block text-gray-700 dark:text-gray-200 font-medium mb-2">
                {{ trans('auth.password_label') }}
            </label>
            <div class="relative">
                <input
                    :type="showPassword ? 'text' : 'password'"
                    id="password"
                    v-model="password"
                    :placeholder="trans('auth.password_placeholder')"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-300 dark:focus:border-blue-400"
                />
                <font-awesome-icon
                    :icon="showPassword ? 'eye-slash' : 'eye'"
                    class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 cursor-pointer hover:text-gray-700 dark:hover:text-gray-200"
                    @click="togglePasswordVisibility"
                />
            </div>
        </div>

        <!-- Bekrefte passordfelt -->
        <div class="mb-4">
            <label for="confirmPassword" class="block text-gray-700 dark:text-gray-200 font-medium mb-2">
                {{ trans('auth.confirm_label') }}
            </label>
            <div class="relative">
                <input
                    :type="showConfirmPassword ? 'text' : 'password'"
                    id="confirmPassword"
                    v-model="confirmPassword"
                    :placeholder="trans('auth.password_placeholder')"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-300 dark:focus:border-blue-400"
                />
                <font-awesome-icon
                    :icon="showConfirmPassword ? 'eye-slash' : 'eye'"
                    class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 cursor-pointer hover:text-gray-700 dark:hover:text-gray-200"
                    @click="toggleConfirmPasswordVisibility"
                />
            </div>
        </div>

        <!-- Generer tilfeldig passord -->
        <div class="mb-4">
            <button
                type="button"
                @click="generatePassword"
                class="w-full px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 dark:bg-yellow-600 dark:hover:bg-yellow-700 transition focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-500"
            >
                <font-awesome-icon icon="random" class="mr-2"/>
                {{ trans('auth.generate_button') }}
            </button>
        </div>
        <p v-if="errorMessage" class="text-red-500 text-sm mt-2">
            {{ errorMessage }}
        </p>

        <!-- Naviger og endre passord -->
        <div class="flex items-center justify-between">
            <BackButton @goBack="emits('goBack')"/>
            <NextButton @goNext="goNext" :isLoading="isLoading" :disabled="!password || !confirmPassword"/>
        </div>
</template>

<script setup>
import {ref} from 'vue';
import {trans} from 'laravel-vue-i18n'; // Importer trans for oversettelser
import axios from "axios";
import BackButton from "../utils/BackButton.vue";
import NextButton from "../NextButton.vue";

// State for felt
const password = ref('');
const confirmPassword = ref('');
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const errorMessage = ref('');
const isLoading = ref(false);

const emits = defineEmits(['goBack', 'reset', 'goNext']);
const props = defineProps({
    selectedUser: {
        type: Object,
        required: true
    },
    isReset: {
        type: Boolean,
        default: false
    }
});

// Funksjon for å vise/skjule passord
const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const toggleConfirmPasswordVisibility = () => {
    showConfirmPassword.value = !showConfirmPassword.value;
};

// Funksjon for å generere et tilfeldig passord
const generatePassword = () => {
    const generated = Math.random().toString(36).slice(-10); // Genererer tilfeldig streng
    password.value = generated;
    confirmPassword.value = generated;
    alert(trans('auth.generated', {password: generated})); // Bruker trans for melding
};

// Funksjon for å håndtere innsending
function goNext() {
    errorMessage.value = '';
    if (password.value === confirmPassword.value) {
        if (props.isReset) {
            axios.post('/api/users/reset_password', {
                user: props.selectedUser.id,
                password: password.value,
                password_confirmation: confirmPassword.value,
            }).then(response => {
                if (response.data.success) {
                    emits('goNext');
                } else {
                    errorMessage.value = response.data.message;
                }
            }).catch(error => {
                errorMessage.value = error.response.data.message;
            });
        }
        else {
            emits('goNext',{pd:password});
        }
    } else {
        errorMessage.value = trans('auth.password_match_error'); // Passordene matcher ikke
    }
}
</script>

<style scoped>
/* Valgfri styling */
</style>
