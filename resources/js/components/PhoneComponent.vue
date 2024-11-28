<template>
    <div class="text-gray-800">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
            {{ guardian ? $t('auth.guardian_phone_label') : $t('auth.phone_label') }} 
            <span class="text-red-700">*</span>
        </label>
        
        <input
            id="phone"
            type="text"
            v-model="phone"
            required
            :placeholder="guardian ? $t('auth.guardian_phone_placeholder') : $t('auth.phone_placeholder')"
            @focus="clearPhoneError"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            :class="{'border-red-700': hasPhoneError}"
        >
        
        <span 
            v-if="hasPhoneError"
            class="m-5 block text-red-700" 
        >
            {{ hasErrors.phone }}
        </span>
        
        <span 
            v-if="!guardian"
            class="block text-sm hover:cursor-pointer my-2 text-blue-700" 
            @click="handleEmailClick"
        >
            {{ $t('auth.use_email_instead') }}
        </span>
        
        <ButtonBar 
            :prev="prev"
            :next="true" 
            :required="true"
            :current-step="currentStep"
            @go="finder"
            @close="handleClose"
            @back="handleBack"
        />
    </div>
</template>

<script setup>
import ButtonBar from "./ButtonBar.vue";
import { ref, computed } from 'vue';
import axios from "axios";
import { trans } from 'laravel-vue-i18n'

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

const emit = defineEmits(['sendErrors', 'success', 'close', 'email', 'back']);

const phone = ref(null);
const hasPhoneError = computed(() => props.hasErrors.phone);

const finder = async () => {
    if (!phone.value) {
        emit('sendErrors', { phone: t('auth.validation.phone_required') });
        return;
    }

    try {
        const response = await axios.post('/api/users/validate_phone', { 
            phone: phone.value 
        });
        
        handleResponse(response.data);
    } catch (error) {
        console.error('API Error:', error);
        emit('sendErrors', { phone: trans('auth.validation.phone_error') });
    }
};

const handleResponse = (data) => {
    if (data === 0) {
        emit('sendErrors', { phone: trans('auth.validation.phone_invalid') });
    } else {
        emit('success', { 
            data: data === 1 ? 1 : data, 
            phone: phone.value,
            isGuardian: props.guardian
        });
    }
};

const clearPhoneError = () => emit('sendErrors', { phone: false });
const handleEmailClick = () => emit('email');
const handleClose = () => {
    if (props.guardian) {
        emit('back');  // Gå tilbake til forrige steg for foresatt
    } else {
        emit('close');  // Lukk for vanlig bruker
    }
};

const handleBack = () => {
    emit('back');
};
</script>
