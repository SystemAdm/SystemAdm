<template>
    <form>
    <div class="password-validation">
        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-300 mb-4 mt-3">
            {{ trans('auth.validate_password_for', {name: selectedUser.full_name}) }}
        </h2>

        <!-- Passord input -->
        <div class="mb-4 relative">
            <label for="password" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ trans('auth.enter_password') }}
            </label>
            <div class="relative">
                <input
                    :type="showPassword ? 'text' : 'password'"
                    id="password"
                    v-model="password"
                    :placeholder="trans('auth.password_placeholder')"
                    class="w-full px-4 py-2 focus:ring-1 focus:outline-1 rounded-lg border
                    bg-gray-100
                    border-gray-400
                    dark:bg-gray-600
                    dark:border-gray-600
                    dark:text-gray-200

                    focus:ring-indigo-500

                    placeholder:text-gray-600
                    dark:placeholder:text-gray-300"
                />
                <!-- Show/Hide Password Button -->
                <button
                    type="button"
                    @click="togglePasswordVisibility"
                    class="absolute inset-y-0 right-0 px-3 pb-1 hover:text-blue-500 focus:outline-none"
                >
                    <span v-if="showPassword">
                        <FontAwesomeIcon :icon="['fas','fa-eye-slash']"
                                         :title="trans('auth.hide_password')" />
                    </span>
                    <span v-else>
                        <FontAwesomeIcon :icon="['fas','fa-eye']"
                                         :title="trans('auth.show_password')" />
                    </span>
                </button>
            </div>
            <ErrorMessage :errorMessage="errorMessage" />

            <!-- Legg til en tilbakestillingslenke -->
            <p class="text-sm mt-3 text-blue-600 hover:underline cursor-pointer dark:text-blue-400">
                <a @click="resetPassword">{{ trans('auth.forgot_password') }}</a>
            </p>
        </div>

        <!-- Valider og Tilbake knapper -->
        <div class="flex justify-between">
            <BackButton @goBack="emits('goBack')" />
            <NextButton @goNext="goNext" :isLoading="isLoading" />
        </div>
    </div>
    </form>
</template>

<script setup>
import {ref} from 'vue';
import {trans} from 'laravel-vue-i18n'; // for oversettelser
import axios from "axios";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import BackButton from "../BackButton.vue";
import NextButton from "../NextButton.vue";
import ErrorMessage from "./ErrorMessage.vue";

// Props
const props = defineProps({
    selectedUser: {
        type: Object,
        required: true,
    },
});

// Emitters
const emits = defineEmits(['goBack', 'goNext','reset']);

// Reactive variabler
const password = ref('');
const isLoading = ref(false);
const showPassword = ref(false); // Kontrollerer synlighet av passord
const errorMessage = ref('');

// Toggle for vis/skjul passord
function togglePasswordVisibility() {
    showPassword.value = !showPassword.value; // Bytter mellom true/false
}

// Tilbakestill passord-knapp
function resetPassword() {
    emits('reset');
}

// Valider passord
async function goNext() {
    isLoading.value = true;
    // Sjekk om passordet er tomt
    if (password.value === '') {
        errorMessage.value = trans('auth.password_required'); // Oversatt feilmelding
        isLoading.value = false;
        return;
    }

    // Vent på resultatet av passordvalideringen
    const isValid = await isValidPassword(password.value);
    if (!isValid) {
        errorMessage.value = trans('auth.invalid_password'); // Oversatt ugyldig passordmelding
        isLoading.value = false;
        return;
    }
    isLoading.value = false;
    // Emit bare hvis passordet er gyldig
    emits('goNext', 200);
}

// Dummy passordvalidering
async function isValidPassword(pass) {
    try {
        // Gjør forespørsel til server og returnér resultat
        const result = await axios.post('/api/users/check', {
            u: props.selectedUser.id,
            p: pass,
        });
        return result.data; // Forventet at serveren returnerer true/false
    } catch (error) {
        // Håndter feilmeldinger
        errorMessage.value = error?.response?.data?.message || trans('auth.invalid_password');
        return false; // Returner false ved feil
    }
}
</script>

<style scoped>
.password-validation {
    max-width: 400px;
    margin: 0 auto;
}

/* Justering for å plassere knappen riktig */
.relative {
    position: relative;
}

button.absolute {
    right: 12px; /* Juster avstanden til høyre */
    top: 50%;
    transform: translateY(-50%);
}
</style>
