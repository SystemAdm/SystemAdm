<template>
    <div class="text-gray-800">
        <div class="space-y-4">
            <h3 class="text-xl font-bold mb-4">{{ phone != null?trans('common.phone'):trans('common.email') }}: <strong>{{ phone != null ? phone : email }}</strong></h3>
        </div>
        <div class="space-y-4 mb-6">
            <!-- Eksisterende brukere -->
            <NameSelectComponent :profiles="processedProfiles" @select="selectProfile" />

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
                    {{ trans('auth.create_new_user') }}
                </span>

                <font-awesome-icon
                    :icon="['fas', 'arrow-right']"
                    class="text-gray-500"
                />
            </div>
        </div>

        <ButtonBar
            :prev="prev"
            @handleBack="handleBack"
        />
    </div>
</template>

<script setup>
import ButtonBar from "./ButtonBar.vue";
import {computed} from 'vue';
import {trans} from "laravel-vue-i18n";
import NameSelectComponent from "./NameSelectComponent.vue";

const props = defineProps({
    profiles: {
        type: Object,
        required: true
    },
    phone: {
        type: String,
    },
    email:{
        type: String,
    },
    currentStep: {
        type:Number,
        required: true
    },
    prev: {
        type: Array,
        required: true
    },
    guardian: {
        type: Boolean,
        default: false
    }
});

// Prosesser profiles-dataen til et konsistent array format
const processedProfiles = computed(() => {
    if (!props.profiles) return [];

    return Object.values(props.profiles).map(profile => {
        return {
            ...profile
        }
    });
});
const handleBack = (step) => {emit('handleBack', step)};
const emit = defineEmits(['handleBack', 'handleClose', 'selectProfile', 'createNew']);

const selectProfile = (user) => {
    console.log('SelectNameComponent: selectProfile called with user:', user);
    emit('selectProfile', user);
};

const createNewUser = () => {
    console.log('Creating new user');
    emit('createNew');
};
</script>
