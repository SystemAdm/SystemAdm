<template>
    <div class="mb-4 mt-4">
        <!-- Type og kontaktinformasjon -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ trans('auth.verification_type') }}
            </label>
            <input
                type="text"
                :value="trans('auth.'+innData.type)"
                readonly
                class="w-full px-4 py-2 mt-1 border rounded-lg bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-400"
            />
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ trans('auth.contact_input') }}
            </label>
            <input
                type="text"
                :value="innData.value"
                readonly
                class="w-full px-4 py-2 mt-1 border rounded-lg bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-400"
            />
        </div>

        <!-- Send kode-knapp -->
        <div class="mb-4 mt-4">
            <button
                @click="goCode"
                :disabled="isSending || isCooldown"
                class="w-full px-4 py-2 text-sm font-medium focus:ring-1 focus:outline-1 border rounded-lg
                    bg-green-500
                    border-green-900
                    text-white
                    focus:ring-green-500
                    dark:bg-green-600
                    dark:border-green-500
                    dark:focus:ring-green-700
                    disabled:text-opacity-50"
                :class="{'hover:bg-green-600 dark:hover:bg-green-700 transition':!isSending}"
            >
                <span v-if="isSending" class="mr-2">
                    <font-awesome-icon :icon="['fas', 'spinner']" spin/>
                    {{ trans('auth.sending_code') }}
                </span>
                <span v-else>{{ buttonText }}</span>
            </button>
        </div>

        <!-- Felt for 6 tall -->
        <div class="flex gap-2 justify-center mb-6">
            <input
                v-for="(digit, index) in code"
                :key="index"
                type="text"
                v-model="code[index]"
                @input="(e) => updateCode(index, e)"
                @keydown="onKeyDown"
                maxlength="1"
                inputmode="numeric"
                autocomplete="one-time-code"
                @keydown.backspace="handleBackspace(index, $event)"
                :data-index="index"
                @paste="onPaste"
                class="w-12 h-12 text-center text-xl border rounded-lg dark:border-gray-700 dark:text-gray-700 focus:outline-none"
            />
        </div>

        <!-- Feilmelding -->
        <ErrorMessage :errorMessage="errorMessage" />

        <!-- Tilbake og valider -->
        <div class="flex justify-between">
            <BackButton @goBack="emits('goBack')" />
            <NextButton @goNext="goNext" :isLoading="isLoading" :isDisabled="isDisabled" />
        </div>
    </div>
</template>

<script setup>
import {computed, onMounted, ref} from 'vue';
import {trans} from 'laravel-vue-i18n';
import axios from 'axios';
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import BackButton from "../utils/BackButton.vue";
import NextButton from "../utils/NextButton.vue";
import ErrorMessage from "./ErrorMessage.vue";

// Props og Emits
const props = defineProps({
    innData: {type:Object,required:true},
});
const emits = defineEmits(['goBack', 'goNext']);

// Reaktive variabler
const code = ref(['', '', '', '', '', '']); // Et array av 6 tomme verdier for koden
const isSending = ref(false);
const isCooldown = ref(false);
const isLoading = ref(false);
const isVerified = ref(false);
const errorMessage = ref('');
const sentMessage = ref('');
const buttonText = ref(trans('auth.send_code'));
const cooldownTime = ref(0);
const isDisabled = computed(() => code.value.some((digit) => digit === '')); // Knappen aktiveres når alle felt er fylt

// Håndterer sending av verifikasjonskode
const goCode = async () => {
    if (isSending.value || isCooldown.value) return;

    isSending.value = true;
    errorMessage.value = '';

    const nextInput = document.querySelector(`input[data-index="0"]`);
    if (nextInput) {
        nextInput.focus();
    }

    try {
        const response = await axios.post('/api/send-code', {
            contact: props.innData.value,
            channel: props.innData.type
        });
        if (response.data.success) {
            sentMessage.value = response.data.message;
            startCooldown(30);
        } else {
            errorMessage.value = response.data.message;
        }
    } catch (error) {
        errorMessage.value = error?.response?.data?.message || trans('auth.error_while_sending_code');
    }
    isSending.value = false;
};

// Start nedtelling
const startCooldown = (seconds) => {
    cooldownTime.value = seconds;
    isCooldown.value = true;

    const interval = setInterval(() => {
        cooldownTime.value -= 1; // Reduser tiden med 1 sekund
        buttonText.value = `${trans('auth.send_new_code')} (${cooldownTime.value}s)`; // Oppdater tekst med nedtelling

        if (cooldownTime.value <= 0) {
            clearInterval(interval); // Stopp nedtellingen
            isCooldown.value = false; // Aktiver knappen igjen
            buttonText.value = trans('auth.send_new_code'); // Tilbakestill knappeteksten
        }
    }, 1000); // Oppdater hvert sekund
};

// Håndterer validering av kode
const goNext = async () => {
    errorMessage.value = '';
    isLoading.value = true;
    const enteredCode = code.value.join(''); // Sammenføy alle tallene til én streng
    if (enteredCode.length !== 6) {
        errorMessage.value = trans('auth.complete_code');
        return;
    }

    try {
        const response = await axios.post('/api/verify-code', {
            contact: props.innData.value,
            code: enteredCode,
            channel: props.innData.type,
        });
        if (response.data.success) {
            isVerified.value = true;
        } else {
            errorMessage.value = response.data.message || trans('auth.invalid_code');
        }
    } catch (error) {
        errorMessage.value = error?.response?.data?.message || trans('auth.invalid_code');
    }
    isLoading.value = false;
    if (isVerified.value) {
        emits('goNext');
    }
};

// Oppdater kodefeltet og flytt fokus til neste input, og håndter pasting
const updateCode = (index, event) => {
    const value = event.target.value;

    // Fjern ugyldige verdier (kun ett tall er tillatt per felt)
    if (!/^\d$/.test(value)) {
        event.target.value = '';
        return;
    }

    // Oppdater koden
    code.value[index] = value;

    // Flytt fokus til neste input-felt
    if (index < code.value.length - 1) {
        const nextInput = document.querySelector(`input[data-index="${index + 1}"]`);
        if (nextInput) {
            nextInput.focus();
        }
    }
};

const handleBackspace = (index, event) => {
    if (event.key === 'Backspace' && !code.value[index]) {
        // Flytt fokus til forrige felt hvis nåværende er tomt
        if (index > 0) {
            const prevInput = document.querySelector(`input[data-index="${index - 1}"]`);
            code.value[index - 1] = '';
            if (prevInput) prevInput.focus();
        }
    } else if (event.key === 'Backspace') {
        code.value[index] = ''; // Tøm nåværende felt
    }
};

const onKeyDown = (event) => {
    // Tillatte taster
    const allowedKeys = ['Backspace', 'ArrowLeft', 'ArrowRight', 'Tab'];
    const isNumber = /^\d$/.test(event.key);

    if (!isNumber && !allowedKeys.includes(event.key)) {
        event.preventDefault();
    }
};

const onPaste = (event) => {
    event.preventDefault(); // Hindre standard oppførsel

    const pastedData = (event.clipboardData || window.clipboardData).getData('text');
    if (!/^\d+$/.test(pastedData)) return; // Tillat bare tall

    setTimeout(() => {
        pastedData.split('').forEach((char, i) => {
            if (i < code.value.length) {
                code.value[i] = char;
            }
        });

        const nextInputIndex = pastedData.length < code.value.length ? pastedData.length : code.value.length - 1;
        const nextInput = document.querySelector(`input[data-index="${nextInputIndex}"]`);
        if (nextInput) {
            nextInput.focus();
        }
    }, 0);
};

onMounted(()=>{
    console.log(props.innData);
})
</script>

<style scoped>
/* Legg til styling hvis ønskelig */
</style>
