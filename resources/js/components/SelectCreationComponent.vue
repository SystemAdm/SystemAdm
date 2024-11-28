<template>
    <div class="text-gray-800">
        <div class="space-y-4 mb-6">
            <!-- QR Kode valg - tilgjengelig for alle -->
            <div 
                @click="handleShowQR"
                class="p-4 border rounded-lg hover:bg-gray-50 cursor-pointer flex items-center justify-between"
            >
                <span class="flex items-center">
                    <font-awesome-icon 
                        :icon="['fas', 'qrcode']" 
                        class="mr-3 text-gray-600"
                    />
                    {{ $t('auth.show_qr') }}
                </span>
                
                <font-awesome-icon 
                    :icon="['fas', 'arrow-right']" 
                    class="text-gray-500"
                />
            </div>

            <!-- Logg inn - kun for aktive brukere -->
            <div 
                v-if="user?.active"
                @click="handleLogin"
                class="p-4 border rounded-lg hover:bg-gray-50 cursor-pointer flex items-center justify-between"
            >
                <span class="flex items-center">
                    <font-awesome-icon 
                        :icon="['fas', 'right-to-bracket']" 
                        class="mr-3 text-blue-600"
                    />
                    {{ $t('auth.login') }}
                </span>
                
                <font-awesome-icon 
                    :icon="['fas', 'arrow-right']" 
                    class="text-gray-500"
                />
            </div>

            <!-- Registrer bruker - kun for inaktive brukere -->
            <div 
                v-if="!user?.active"
                @click="handleRegister"
                class="p-4 border rounded-lg hover:bg-gray-50 cursor-pointer flex items-center justify-between"
            >
                <span class="flex items-center">
                    <font-awesome-icon 
                        :icon="['fas', 'user-plus']" 
                        class="mr-3 text-green-600"
                    />
                    {{ $t(isGuardian ? 'auth.register_guardian' : 'auth.register_user') }}
                </span>
                
                <font-awesome-icon 
                    :icon="['fas', 'arrow-right']" 
                    class="text-gray-500"
                />
            </div>
        </div>

        <ButtonBar 
            :prev="prev"
            :current-step="currentStep"
            @back="handleBack"
        />
    </div>
</template>

<script setup>
import ButtonBar from "./ButtonBar.vue";

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
    isGuardian: {
        type: Boolean,
        default: false
    },
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

const emit = defineEmits(['back', 'showQR', 'login', 'register', 'reset']);

// Hendelseshåndteringsmetoder
const handleShowQR = () => emit('showQR');
const handleLogin = () => emit('login');
const handleRegister = () => emit('register');
const handleBack = () => emit('back');
</script>
