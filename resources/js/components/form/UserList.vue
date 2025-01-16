<template>
    <div class="mt-4">
        <!-- Overskrift og gå tilbake -->
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-medium text-gray-800 dark:text-gray-100">
                {{ props.guradian?trans('auth.select_guardian'):trans('auth.select_user') }}
            </h3>
        </div>

        <!-- Opplysende tekst -->
        <p class="text-sm text-gray-600 dark:text-gray-400 pb-4">
            <span v-html="trans('auth.user_list_info, :input', { input: innData.value })"></span>
        </p>

        <!-- Liste over brukere -->
        <ul class="space-y-4">
            <li
                v-for="(user, id) in users"
                :key="id"
                @click="selectUser(user)"
                class="p-4 flex items-center justify-between bg-gray-100 border border-gray-300 rounded-lg
                       cursor-pointer hover:bg-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
            >
                <!-- Avatar eller siluette -->
                <div class="flex items-center">
                    <template v-if="user.profile && user.profile.avatar">
                        <!-- Viser avatar hvis finnes -->
                        <img
                            :src="user.profile.avatar"
                            alt="Avatar"
                            class="w-12 h-12 rounded-full mr-4"
                        />
                    </template>
                    <template v-else>
                        <!-- Viser siluetteikon hvis ingen avatar -->
                        <font-awesome-icon
                            :icon="['fas', 'user']"
                            class="text-gray-500 w-8 h-8 mr-4 dark:text-gray-300"
                        />
                    </template>

                    <!-- Brukernavn -->
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                            {{ user.given_name }} {{ user.family_name }}
                        </p>
                    </div>
                </div>

                <!-- Statusikon (aktiv/inaktiv) -->
                <font-awesome-icon
                    :icon="user.active ? ['fas', 'lock']: ['fas', 'lock-open']"
                    :class="user.active ? 'text-yellow-500': 'text-green-500' "
                    class="text-lg"
                />
            </li>
        </ul>

        <!-- Legg til nytt navn -->
        <!--
        <div
            @click="addNewUser"
            class="mt-6 p-4 flex items-center justify-between bg-blue-100 border border-blue-300 rounded-lg
                   cursor-pointer hover:bg-blue-200 dark:bg-blue-800 dark:border-blue-700 dark:hover:bg-blue-700"
        >
            <div class="flex items-center">
                <font-awesome-icon
                    :icon="['fas', 'circle-plus']"
                    class="text-blue-500 w-8 h-8 mr-4 dark:text-blue-300"
                />
                <p class="text-sm font-medium text-blue-800 dark:text-blue-200">
                    {{ trans('auth.add_new_name') }}
                </p>
            </div>
        </div>
        <div class="flex justify-start mt-6">
            <BackButton @goBack="goBack" />
        </div>-->
    </div>
</template>

<script setup>
import {trans} from 'laravel-vue-i18n';
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import BackButton from "../utils/BackButton.vue";

// Props: Data fra API og input fra brukeren
const props = defineProps({
    users: {
        type: Object, // Brukere returnert fra API
        required: true
    },
    innData: {
        type: Object, // Input gitt av brukeren (epost eller telefonnummer)
        required: true
    },
    guradian: {
        type: Boolean,
        default: false
    },
});

// Emit-events til foreldren
const emits = defineEmits(['selectUser', 'addNew', 'goBack']);

// Velg en bruker
function selectUser(user) {
    emits('selectUser', user); // Sender valgt bruker til forelderen
}

// Legg til nytt navn
function addNewUser() {
    emits('addNew'); // Varsler foreldren om at nytt navn skal legges til
}

// Gå tilbake til forrige steg
function goBack() {
    emits('goBack'); // Varsler foreldren om at brukeren vil gå tilbake
}
</script>
