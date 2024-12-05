<template>
    <div class="password-check">
        <div class="mb-5">
            <PasswordInput
                id="password"
                :modelValue="password"
                :labelKey="'auth.password'"
                :placeholderKey="'auth.password_placeholder'"
                :infoKey="'auth.password_info'"
                :error="hasErrors.password"
                @update:modelValue="updatePassword"
                :isLoading="isLoading"
                @clearError="clearError"></PasswordInput>
        </div>

        <ButtonBar
            :hasNext="true"
            :prev="prev"
            :current-step="currentStep"
            :hasRequired="true"
            @handleNext="check"
            @handleBack="handleBack"
            @handleClose="handleClose"
        />
    </div>
</template>

<script setup>
import ButtonBar from "./ButtonBar.vue";
import axios from "axios";
import {onMounted, ref} from 'vue';
import PasswordInput from "./fields/PasswordInput.vue";

const props = defineProps({
    selected: {
        type: Object,
        required: true
    },
    hasErrors: {
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
    },
    currentStep: {
        type: Number,
        required: true
    }
});

const emit = defineEmits(['sendErrors', 'handleClose', 'handleBack', 'handleSuccess']);

const password = ref('');
const isLoading = ref(false);
const passwordInput = ref(null);

// Hendelseshåndteringsmetoder
const clearError = () => emit('sendErrors', {password: false});
const handleBack = (step) => emit('handleBack', step);
const handleClose = () => emit('handleClose');  // Ny metode for å håndtere close
const updatePassword = (string) => password.value = string;
const check = async () => {
    if (!password.value) {
        emit('sendErrors', {password: 'auth.password_required'});
        return;
    }

    isLoading.value = true;

    try {
        const response = await axios.post('/api/users/check', {
            p: password.value,
            u: props.selected.id
        });

        if (response.data === true) {
            emit('handleSuccess');
        } else {
            emit('sendErrors', {password: 'auth.password_incorrect'});
        }
    } catch (error) {
        console.error('Password check error:', error);
        emit('sendErrors', {
            password: error.response?.data?.message || 'auth.password_check_error'
        });
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    passwordInput.value?.focus();
});
</script>
