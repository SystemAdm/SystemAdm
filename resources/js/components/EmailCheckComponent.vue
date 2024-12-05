<template>
    <div class="text-gray-800">

        <EmailInput
            id="email"
            :modelValue="email"
            :labelKey="guardian ? 'auth.guardian_email_label':'auth.email_label'"
            :placeholderKey="guardian ? 'auth.guardian_email_placeholder':'auth.email_placeholder'"
            :error="hasError"
            @update:modelValue="updateEmail"
            :isLoading="isLoading"
            @clearError="clearEmailError"
        ></EmailInput>

        <span
                        class="block text-sm hover:cursor-pointer my-2 text-blue-700"
            @click="handlePhoneClick"
        >
            {{ trans('auth.use_phone_instead') }}
        </span>

        <ButtonBar
            :current-step="currentStep"
            :hasNext="true"
            :hasRequired="true"
            @handleClose="handleClose"
            @handleNext="validateEmail"
        />
    </div>
</template>

<script setup>
import {computed, ref} from 'vue';
import { trans } from 'laravel-vue-i18n'
import axios from 'axios';
import ButtonBar from "./ButtonBar.vue";
import EmailInput from "./fields/EmailInput.vue";

const props = defineProps({
    hasErrors: {
        type: Object,
        required: true
    },
    prev: {
        type: Array,
        default: () => []
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

const emit = defineEmits(['sendErrors', 'handleSuccess', 'handleClose', 'handleBack','handlePhone']);

const email = ref('');
const hasError = computed(()=>props.hasErrors.email);
const updateEmail = (string) => email.value = string;
const isLoading = ref(false);
const handlePhoneClick = () => {
    emit('handlePhone');
}
const clearEmailError = () => {
    emit('sendErrors', { email: false });
};

const handleClose = () => {
    emit('handleClose');
};

const handleBack = (step) => {
    emit('handleBack', step);
};

const validateEmail = async () => {
    if (!email.value) {
        emit('sendErrors', { email: trans('validation.email_required') });
        return;
    }

    try {
        const response = await axios.post('/api/users/validate_email', {
            email: email.value
        });

        if (response.data === 0) {
            emit('sendErrors', { email: trans('validation.email_invalid') });
            return;
        }

        if (response.data === 1) {
            // Valid email, not in database - go to NAME (step 5)
            emit('handleSuccess', {
                email: email.value
            });
            return;
        }

        if (typeof response.data === 'object') {
            // Email exists in database - go to SELECT_NAME (step 3)
            emit('handleSuccess', {
                email: email.value,
                data: response.data
            }, 3);

        }

    } catch (error) {
        console.error('Email validation error:', error);
        emit('sendErrors', { email: trans('errors.validation_failed') });
    }
};
</script>
