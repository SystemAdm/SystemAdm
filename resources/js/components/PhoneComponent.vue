<template>
    <div>
        <h2 v-if="guardian" class="text-3xl">{{ guardianTitle }}</h2>
        
        <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
            {{ $t('auth.phone_number') }} <span class="text-red-700">*</span>
        </label>
        
        <input
            id="phone"
            type="text"
            v-model="phone"
            required
            :placeholder="$t('auth.phone_placeholder')"
            @focus="$emit('sendErrors', {phone: false})"
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
            class="block text-sm hover:cursor-pointer my-2 text-blue-700" 
            @click="$emit('email')"
        >
            {{ $t('auth.login_with_email') }}
        </span>
        
        <ButtonBar 
            :next="true" 
            :required="true" 
            @go="finder" 
            @close="$emit('close')"
        />
    </div>
</template>
<script setup>
import ButtonBar from "./ButtonBar.vue";
import { ref, computed } from 'vue';
import axios from "axios";

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
    }
});

const emit = defineEmits(['sendErrors', 'success', 'close', 'email', 'back']);

const phone = ref(null);

// Computed properties
const hasPhoneError = computed(() => props.hasErrors.phone);
const guardianTitle = computed(() => props.guardian ? window.i18n.t('auth.guardian_parent') : '');

// Methods
const clearPhoneError = () => emit('sendErrors', { phone: false });

const validatePhone = async () => {
    if (!phone.value) {
        emit('sendErrors', { phone: window.i18n.t('auth.field_required') });
        return false;
    }
    return true;
};

const handleEmailClick = () => emit('email');
const handleClose = () => emit('close');

const finder = async () => {
    if (await validatePhone()) {
        try {
            const response = await axios.post('/api/users/validate_phone', { 
                phone: phone.value 
            });
            
            handleResponse(response.data);
        } catch (error) {
            console.error('API Error:', error);
            emit('sendErrors', { phone: window.i18n.t('auth.validation_error') });
        }
    }
};

const handleResponse = (data) => {
    if (data === 0) {
        emit('sendErrors', { phone: window.i18n.t('auth.invalid_phone') });
    } else {
        emit('success', { 
            data: data === 1 ? 1 : data, 
            phone: phone.value 
        });
    }
};
</script>
