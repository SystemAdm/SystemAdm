<template>
    <div class="text-gray-800">
        <div class="space-y-4 mb-6">
            <!-- Eksisterende brukere -->
            <template v-if="processedProfiles.length > 0">
                <div 
                    v-for="user in processedProfiles" 
                    :key="user.id"
                    @click="selectProfile(user)"
                    class="p-4 border rounded-lg hover:bg-gray-50 cursor-pointer flex items-center justify-between"
                >
                    <span class="flex items-center">
                        <font-awesome-icon 
                            :icon="['fas', 'user']" 
                            class="mr-3 text-gray-600"
                        />
                        {{ user.full_name }}
                    </span>
                    
                    <font-awesome-icon 
                        v-if="user.active"
                        :icon="['fas', 'lock']" 
                        class="text-gray-500"
                        :title="$t('auth.password_required')"
                    />
                </div>
            </template>

            <!-- Opprett ny bruker valg -->
            <div 
                @click="createNewUser"
                class="p-4 border rounded-lg hover:bg-gray-50 cursor-pointer flex items-center justify-between text-blue-600"
            >
                <span class="flex items-center">
                    <font-awesome-icon 
                        :icon="['fas', 'user-plus']" 
                        class="mr-3"
                    />
                    {{ $t('auth.create_new_user') }}
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
            @back="$emit('back')"
        />
    </div>
</template>

<script setup>
import ButtonBar from "./ButtonBar.vue";
import { computed } from 'vue';

const props = defineProps({
    user: {
        type: Object,
        required: false,
        default: () => null
    },
    profiles: {
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

// Prosesser profiles-dataen til et konsistent array format
const processedProfiles = computed(() => {
    if (!props.profiles) return [];
    
    // Hvis profiles er et array
    if (Array.isArray(props.profiles)) {
        return props.profiles.map(profile => {
            const userId = Object.keys(profile)[0];
            return profile[userId];
        });
    }
    
    // Hvis profiles er et enkelt objekt
    if (typeof props.profiles === 'object' && props.profiles !== null) {
        const userId = Object.keys(props.profiles)[0];
        return userId ? [props.profiles[userId]] : [];
    }
    
    return [];
});

const emit = defineEmits(['back', 'showQR', 'login', 'register', 'reset', 'profileSelect', 'createNew']);

const selectProfile = (user) => {
    console.log('SelectNameComponent: selectProfile called with user:', user);
    emit('profileSelect', user);
};

const createNewUser = () => {
    console.log('Creating new user');
    emit('createNew');
};
</script>