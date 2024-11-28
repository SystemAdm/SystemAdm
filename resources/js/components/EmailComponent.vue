<template>
    <div class="max-w-2xl mx-auto">
        <h2 v-if="guardian" class="text-3xl mb-6">{{ $t('common.guardian_parent') }}</h2>
        
        <EmailInput
            id="email"
            v-model="email"
            :error="hasErrors.email"
            label-key="common.email"
            info-key="common.email_usage_info"
            placeholder-key="common.email_placeholder"
            @clear-error="clearError('email')"
        />

        <EmailInput
            id="email_confirm"
            v-model="emailConfirm"
            :error="hasErrors.email_confirm"
            label-key="common.email_confirm"
            info-key="common.email_confirm_info"
            placeholder-key="common.email_confirm_placeholder"
            @clear-error="clearError('email_confirm')"
        />

        <div class="mb-5">
            <span 
                class="block text-sm hover:cursor-pointer text-blue-700"
                @click="handlePhoneClick"
            >
                {{ $t('common.login_with_phone') }}
            </span>
        </div>

        <ButtonBar 
            :prev="prev"
            :next="true"
            :current-step="currentStep"
            @go="validateEmail"
            @back="handleBack"
        />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { trans } from 'laravel-vue-i18n'
import axios from 'axios';
import ButtonBar from './ButtonBar.vue';
import EmailInput from './fields/EmailInput.vue';

const props = defineProps({
    hasErrors: {
        type: Object,
        required: true
    },
    prev: {
        type: Array,
        required: true
    },
    guardian: {
        type: Boolean,
        default: false
    },
    currentStep: {
        type: Number,
        required: true
    }
});

const emit = defineEmits(['sendErrors', 'phone', 'success', 'back']);

const email = ref('');
const emailConfirm = ref('');

const clearError = (field) => {
    emit('sendErrors', { [field]: false });
};

const handlePhoneClick = () => {
    emit('phone');
};

const handleBack = () => {
    emit('back');
};

const validateEmail = async () => {
    // Reset errors
    emit('sendErrors', { email: false, email_confirm: false });

    // Validate required fields
    if (!email.value) {
        emit('sendErrors', { email: trans('validation.required', { field: trans('common.email') }) });
        return;
    }
    if (!emailConfirm.value) {
        emit('sendErrors', { email_confirm: trans('validation.required', { field: trans ('common.email_confirm') }) });
        return;
    }

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email.value)) {
        emit('sendErrors', { email: trans('validation.email') });
        return;
    }

    // Validate matching emails
    if (email.value !== emailConfirm.value) {
        emit('sendErrors', { email_confirm: trans('validation.email_mismatch') });
        return;
    }

    try {
        const response = await axios.post('/api/users/verify_email', {
            email: email.value
        });
        emit('success', { email: email.value });
    } catch (error) {
        if (error.response?.status === 422) {
            emit('sendErrors', { email: error.response.data.message });
        } else {
            emit('sendErrors', { email: trans('errors.something_went_wrong') });
        }
    }
};
</script>
