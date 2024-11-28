<template>
    <div class="container mx-auto px-4 py-8 text-black">
        <!-- Loading state -->
        <div v-if="loading" class="text-center">
            <p>{{ trans('profile.loading') }}</p>
        </div>

        <!-- Profile content -->
        <div v-else-if="profile" class="max-w-2xl mx-auto">
            <!-- Legg til tittel og rediger-knapp i samme rad -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">{{ trans('profile.my_profile') }}</h1>
                
                <!-- Rediger-knapp -->
                <button 
                    v-if="!isEditing"
                    @click="isEditing = true"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-200">
                    {{ trans('profile.actions.edit') }}
                </button>
            </div>
            
            <div class="bg-white shadow rounded-lg p-6">
                <!-- Edit Form -->
                <EditProfileForm
                    v-if="isEditing"
                    :profile="profile"
                    @update:profile="handleProfileUpdate"
                    @close="isEditing = false"
                />
                
                <!-- Profile Display -->
                <template v-else>
                    <div class="mb-4">
                        <h2 class="text-lg font-semibold">{{ profile.given_name }} {{ profile.family_name }}</h2>
                    </div>

                    <!-- Telefonnumre -->
                    <div class="mt-4">
                        <h3 class="font-medium text-gray-700 mb-2">{{ trans('profile.fields.phones') }}</h3>
                        <div v-if="profile.phones?.length" class="space-y-2">
                            <div v-for="phone in profile.phones" :key="phone.id" 
                                 class="flex items-center gap-2 text-gray-600">
                                <span :class="{ 'font-semibold': phone.primary }">
                                    {{ formatPhoneNumber(phone.number, phone.country) }}
                                </span>
                                <span v-if="phone.primary" 
                                      class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded">
                                    {{ trans('profile.primary') }}
                                </span>
                                <span v-if="phone.verified_at" 
                                      class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded"
                                      :title="trans('profile.verified_at', { date: formatDate(phone.verified_at) })">
                                    {{ trans('profile.verified') }}
                                </span>
                                <span v-if="phone.pivot.verified_at"
                                      class="text-xs bg-purple-100 text-purple-800 px-2 py-0.5 rounded"
                                      :title="trans('profile.user_verified_at', { date: formatDate(phone.pivot.verified_at) })">
                                    {{ trans('profile.user_verified') }}
                                </span>
                            </div>
                        </div>
                        <p v-else class="text-gray-500">{{ trans('profile.no_phones') }}</p>
                    </div>

                    <!-- E-postadresser -->
                    <div class="mt-4">
                        <h3 class="font-medium text-gray-700 mb-2">{{ trans('profile.fields.emails') }}</h3>
                        <div v-if="profile.emails?.length" class="space-y-2">
                            <div v-for="email in profile.emails" :key="email.id" 
                                 class="flex items-center gap-2 text-gray-600">
                                <span :class="{ 'font-semibold': email.pivot.is_primary }">
                                    {{ email.email }}
                                </span>
                                <span v-if="email.pivot.is_primary" 
                                      class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded">
                                    {{ trans('profile.primary') }}
                                </span>
                                <span v-if="email.pivot.is_verified" 
                                      class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">
                                    {{ trans('profile.verified') }}
                                </span>
                            </div>
                        </div>
                        <p v-else class="text-gray-500">{{ trans('profile.no_emails') }}</p>
                    </div>

                    <!-- Permissions og Roller -->
                    <div v-if="profile.permissions || profile.roles" class="mt-6 p-4 bg-gray-50 rounded">
                        <div v-if="profile.roles?.length" class="mb-3">
                            <h3 class="font-semibold mb-1">{{ $t('profile.details.roles') }}:</h3>
                            <div class="flex gap-2">
                                <span 
                                    v-for="role in profile.roles" 
                                    :key="role"
                                    class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">
                                    {{ role.name }}
                                </span>
                            </div>
                        </div>
                        
                        <div v-if="profile.permissions?.length">
                            <h3 class="font-semibold mb-1">{{ $t('profile.details.permissions') }}:</h3>
                            <div class="flex flex-wrap gap-2">
                                <span 
                                    v-for="permission in profile.permissions" 
                                    :key="permission"
                                    class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">
                                    {{ permission }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Edit knapp -->
                    <div v-if="can('edit_profile')" class="mt-6">
                        <button 
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-200"
                            @click="isEditing = true">
                            {{ $t('profile.actions.edit') }}
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Error state -->
        <div v-else class="text-center text-red-600">
            <p>{{ $t('profile.error.load') }}</p>
            <button 
                @click="router.push('/login')"
                class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-200">
                {{ $t('profile.error.go_to_login') }}
            </button>
        </div>
    </div>
</template>
<script setup>
import { ref, onMounted, computed, inject } from 'vue';
import { useRouter } from 'vue-router';
import { notify } from './../utils/notify';
import EditProfileForm from '../profile/EditProfileForm.vue';
import { trans } from 'laravel-vue-i18n';
import axios from 'axios';
const props = defineProps({
    me: {
        type: Object,
        default: null
    }
});

const router = useRouter();
const profile = ref(null);
const loading = ref(true);
const isEditing = ref(false);
const fetchAuthenticatedUser = inject('fetchAuthenticatedUser');

const can = (permission) => {
    return window.Laravel?.permissions?.includes(permission) || false;
};

onMounted(async () => {
    try {
        loading.value = true;
        
        if (!props.me) {
            await fetchAuthenticatedUser();
        }
        
        const response = await axios.get('/api/user');
        profile.value = response.data;
        
    } catch (error) {
        console.error('Error fetching profile:', error);
        if (error.response?.status === 401) {
            router.push('/login');
        } else {
            notify({
                group: "error",
                title: "Error",
                text: trans('profile.error.load')
            });
        }
    } finally {
        loading.value = false;
    }
});

const handleProfileUpdate = (updatedProfile) => {
    profile.value = updatedProfile;
    isEditing.value = false;
};

// Hjelpefunksjon for å formatere telefonnummer
const formatPhoneNumber = (number, country) => {
    // Her kan du legge til mer avansert formatering basert på land
    return `${country ? '+' + country + ' ' : ''}${number}`;
};

// Hjelpefunksjon for å formatere datoer
const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('nb-NO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>
