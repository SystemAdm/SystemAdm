<template>
    <div class="mt-4">
        <!-- Toppseksjon: Overskrift -->
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-medium text-gray-800 dark:text-gray-100">
                {{ selectedUser.active === 1 ? trans('user.active_user') : trans('user.inactive_user') }}
            </h3>
        </div>

        <!-- Informativ tekst -->
        <p class="text-sm text-gray-600 dark:text-gray-400 pb-4">
            {{ trans('user.details_info') }}
        </p>

        <!-- Knappseksjon -->
        <div class="grid grid-cols-2 gap-4 content-start">
            <!-- Knapp: Vis QR-kode -->
            <div class="h-32 text-center w-full bg-gray-500 rounded-xl content-center">
                <div>
                    <font-awesome-icon :icon="['fas','qrcode']"/>
                </div>
                <div>{{ trans('user.show_qr_code') }}</div>
            </div>

            <!-- Knapp: Logg inn (hvis brukeren er aktiv) -->
            <div
                v-if="selectedUser.active === 1"
                class="h-32 text-center w-full bg-gray-500 rounded-xl content-center
hover:bg-gray-600 dark:hover:bg-gray-700 cursor-pointer"
                @click="login"
            >
                <div>
                    <font-awesome-icon size="2xl" :icon="['fas', 'sign-in']"/>
                </div>
                <div>{{ trans('user.signin') }}</div>
            </div>

            <!-- Knapp: Opprett bruker (hvis brukeren er inaktiv) -->
            <div
                v-if="selectedUser.active === 0"
                class="h-32 text-center w-full bg-gray-500 rounded-xl content-center"
            >
                <div>
                    <font-awesome-icon :icon="['fas', 'user-plus']"/>
                </div>
                <div>{{ trans('user.create_user') }}</div>
            </div>

            <!-- Knapp: Legg til foresatt -->
            <div class="h-32 text-center w-full bg-gray-500 rounded-xl content-center">
                <div>
                    <font-awesome-icon :icon="['fas', 'user-shield']" class="mr-2"/>
                </div>
                <div>{{ trans('user.add_guardian') }}</div>
            </div>

            <!-- Knapp: Se profil -->
            <div class="h-32 text-center w-full bg-gray-500 rounded-xl content-center">
                <div>
                    <font-awesome-icon :icon="['fas','address-card']"/>
                </div>
                <div>{{ trans('user.view_profile') }}</div>
            </div>

            <!-- Knapp: Innstillinger -->
            <div class="h-32 text-center w-full bg-gray-500 rounded-xl content-center">
                <div>
                    <font-awesome-icon :icon="['fas','gear']"/>
                </div>
                <div>{{ trans('user.settings') }}</div>
            </div>
        </div>

        <!-- Liste over foresatte -->
        <div
            v-if="selectedUser.guardians && selectedUser.guardians.length > 0"
            class="mt-6"
        >
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                {{ trans('user.guardian_list') }}
            </h3>
            <ul class="space-y-2">
                <li
                    v-for="guardian in selectedUser.guardians"
                    :key="guardian.id"
                    class="p-2 bg-gray-100 rounded-lg dark:bg-gray-800"
                >
                    {{ guardian.full_name }}
                </li>
            </ul>
        </div>

        <!-- Liste over ungdom -->
        <div
            v-if="selectedUser.children && selectedUser.children.length > 0"
            class="mt-6"
        >
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                {{ trans('user.children_list') }}
            </h3>
            <ul class="space-y-2">
                <li
                    v-for="child in selectedUser.children"
                    :key="child.id"
                    class="p-2 bg-gray-100 rounded-lg dark:bg-gray-800"
                >
                    {{ child.full_name }}
                </li>
            </ul>
        </div>

        <!-- Gå tilbake-knapp -->
        <div class="flex justify-start mt-6">
            <BackButton @goBack="goBack"/>
        </div>
    </div>
</template>

<script setup>
import {trans} from 'laravel-vue-i18n';
import BackButton from "../utils/BackButton.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

// Props: Data fra API
const props = defineProps({
    loggedIn: {type: Boolean, default: false},
    selectedUser: {
        type: Object, // Datastruktur med brukerinformasjon (inkl. relasjoner)
        required: true,
    },
});

// Emit-events til foreldren
const emits = defineEmits(['goBack','goLogin']);

// Gå tilbake til forrige steg
function goBack() {
    emits('goBack'); // Varsler foreldren
}

function login() {
    emits("goLogin");
}
</script>

<style>

</style>
