<template>
    <div class="qr-code-container">
        <div class="mb-6 text-center">
            <h3 class="text-lg font-semibold mb-2">Din QR-kode</h3>
            <p class="text-gray-600 text-sm mb-4">
                Bruk denne QR-koden for å logge inn på Spillhuset
            </p>

            <div class="bg-white p-4 rounded-lg shadow-inner mx-auto max-w-xs">
                <div v-if="loading" class="animate-pulse bg-gray-200 h-48 w-48 mx-auto">
                    <span class="sr-only">Laster QR-kode...</span>
                </div>

                <div v-else-if="error" class="text-red-500 text-center">
                    Kunne ikke laste QR-kode
                </div>

                <div v-else
                     class="mx-auto w-48 h-48"
                     v-html="qrCode"
                     :aria-label="`QR-kode for ${user?.full_name}`">
                </div>
            </div>

            <p class="text-sm text-gray-500 mt-4">{{ user?.full_name }}</p>
            <p class="text-xs text-gray-400 mt-2">
                Skann denne QR-koden med Spillhuset-appen for å logge inn
            </p>
        </div>

        <ButtonBar
            :prev="prev"
            :current-step="currentStep"
            @handleBack="handleBack"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import ButtonBar from "../ButtonBar.vue";
import axios from 'axios';

const props = defineProps({
    currentStep: {
        type: Number,
        required: true
    },
    user: {
        type: Object,
        required: true
    },
    prev: {
        type: Array,
        required: true
    },
    STEPS: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['handleReset','handleBack']);
const qrCode = ref(null);
const error = ref(false);
const loading = ref(true);
const handleReset = () => emit('handleReset');
const handleBack = (step) => emit('handleBack', step);
onMounted(async () => {
    if (!props.user?.id) {
        error.value = true;
        loading.value = false;
        return;
    }

    try {
        const response = await axios.get(`/api/users/${props.user.id}/qr`, {
            headers: {
                'Accept': 'application/json'
            }
        });
        qrCode.value = response.data.svg;
    } catch (err) {
        console.error('Failed to load QR code:', err);
        error.value = true;
    } finally {
        loading.value = false;
    }
});
</script>
