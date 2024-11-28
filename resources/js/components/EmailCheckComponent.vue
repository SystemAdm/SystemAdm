<template>
    <div>
        <EmailInput
            id="email"
            v-model="email"
            :error="hasErrors.email"
            label-key="common.email"
            placeholder-key="common.email_placeholder"
            required
            @clear-error="clearEmailError"
        />
        
        <ButtonBar 
            :current-step="currentStep" 
            :prev="prev" 
            :next="true" 
            @close="handleClose" 
            @go="validateEmail" 
            @back="handleBack"
        />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { trans } from 'laravel-vue-i18n'
import axios from 'axios';
import ButtonBar from "./ButtonBar.vue";
import EmailInput from "./fields/EmailInput.vue";

const props = defineProps({
    prev: {
        type: Array,
        required: true
    },
    hasErrors: {
        type: Object,
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

const emit = defineEmits(['sendErrors', 'success', 'close', 'back']);

const email = ref(null);

const clearEmailError = () => {
    emit('sendErrors', { email: false });
};

const handleClose = () => {
    emit('close');
};

const handleBack = (step) => {
    emit('back', step);
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
            emit('success', {
                email: email.value
            }, 5);
            return;
        }
        
        if (typeof response.data === 'object') {
            // Email exists in database - go to SELECT_NAME (step 3)
            emit('success', {
                email: email.value,
                selection: response.data
            }, 3);
            return;
        }

    } catch (error) {
        console.error('Email validation error:', error);
        emit('sendErrors', { email: trans('errors.validation_failed') });
    }
};
</script>
