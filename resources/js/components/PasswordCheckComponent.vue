<template>
    <div class="password-check">
        <div class="mb-5">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                {{ $t('auth.password') }} <span class="text-red-700">*</span>
                <span class="block text-gray-400 text-sm">
                    {{ $t('auth.password_protected_account') }}
                </span>
            </label>
            
            <div class="relative">
                <input
                    ref="passwordInput"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline pr-10"
                    :class="{'border-red-700': hasErrors.password}"
                    id="password"
                    :type="showPassword ? 'text' : 'password'"
                    :placeholder="$t('auth.password_placeholder')"
                    v-model="password"
                    required
                    @focus="clearError"
                    @keyup.enter="check"
                    :disabled="isLoading">
                    
                <button 
                    type="button"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-600"
                    @click="togglePassword"
                    :disabled="isLoading">
                    <font-awesome-icon 
                        :icon="['fas', showPassword ? 'eye-slash' : 'eye']"
                        class="hover:text-gray-800"
                    />
                </button>
            </div>

            <span 
                v-if="hasErrors.password" 
                class="ml-3 mt-2 block text-red-700 text-sm"
            >
                {{ hasErrors.password }}
            </span>
        </div>

        <ButtonBar 
            :prev="prev" 
            :next="true" 
            :loading="isLoading"
            :current-step="currentStep"
            @close="handleClose"
            @go="check" 
            @back="back"
        />
    </div>
</template>

<script setup>
import ButtonBar from "./ButtonBar.vue";
import axios from "axios";
import { ref, onMounted } from 'vue';

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

const emit = defineEmits(['sendErrors', 'close', 'back', 'success']);

const password = ref('');
const showPassword = ref(false);
const isLoading = ref(false);
const passwordInput = ref(null);

// Hendelseshåndteringsmetoder
const clearError = () => emit('sendErrors', { password: false });
const togglePassword = () => showPassword.value = !showPassword.value;
const back = () => emit('back');
const handleClose = () => emit('close');  // Ny metode for å håndtere close

const check = async () => {
    if (!password.value) {
        emit('sendErrors', { password: 'auth.password_required' });
        return;
    }
    
    isLoading.value = true;
    
    try {
        const response = await axios.post('/api/users/check', {
            p: password.value, 
            u: props.selected.id
        });
        
        if (response.data === true) {
            emit('success', { 
                password: password.value,
                selected: props.selected 
            });
        } else {
            emit('sendErrors', { password: 'auth.password_incorrect' });
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
