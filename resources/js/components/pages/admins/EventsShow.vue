<template>
    <div class="text-gray-800 dark:text-gray-200">
        <!-- Overskrift -->
        <h1 class="text-3xl font-bold mb-4">{{ trans('events.Show') }}</h1>
        <div class="flex space-x-2">
            <BackButton @goBack="router.back()"/>
            <div v-if="event !== null && event.deleted_at === null" class="flex space-x-2">
                <EditButton @goEdit="goEdit"/>
                <DeleteButton @goDelete="goDelete"/>
            </div>
            <div v-else class="flex space-x-2">
                <RecoverButton @goRecover="goRecover"/>
                <PermanentButton @goPermanent="goPermanent"/>
            </div>
        </div>
        <!-- Laster-data -->
        <div v-if="loading" class="text-center">
            <p>
                <font-awesome-icon icon="fa-spinner" spin class="mr-2"/>
                {{ trans('Loading...') }}
            </p>
        </div>

        <!-- Feilmelding -->
        <div v-if="error" class="text-red-500 text-center my-4">
            {{ trans('error_loading') }}: {{ error }}
        </div>

        <!-- Event handling og action bar -->
        <AdminEventActionBar
            v-if="event !== null"
            :event="event"
            @startOver="handleStartOver"
            @signupEndNow="handleSignupEndNow"
            @signupBeginNow="handleSignupBeginNow"
            @eventEndNow="handleEventEndNow"
            @eventBeginNow="handleEventBeginNow"
        />

        <!-- Event-data -->
        <div v-if="!loading && event" class="mt-5">
            <div class="grid md:grid-cols-3 gap-3 space-y-1">
                <div v-for="(users, key) in mappedColumns" :key="key"
                     class="border rounded-lg shadow-md overflow-hidden bg-white dark:bg-gray-800 dark:border-gray-700 hover:shadow-lg transition duration-200 p-2">
                    <p class="font-bold">{{ trans('events.' + key) }}</p>
                    <div v-if="users.length === 0">
                        <p class="text-gray-600 dark:text-gray-400">{{ trans('None') }}</p>
                    </div>
                    <EventUserPill
                        v-for="user in users"
                        :key="user.id"
                        :user="user"
                        :col="key"
                        :event="event.id"
                        :isInsider="isInsider(user)"
                        :isCrew="isCrew(user)"
                        :isAttending="isAttending(user)"
                        :isRegistered="isRegistered(user)"
                        @update="(newCol, u) => update(newCol, u)"
                    />
                </div>
            </div>
        </div>

        <!-- Ingen events -->
        <div v-else-if="!loading">
            <p class="text-center text-gray-600 dark:text-gray-400">
                {{ trans('events.NoEvents') }}.
            </p>
        </div>
    </div>
</template>

<script setup>
import {computed, onMounted, ref, watch} from "vue";
import {useRoute, useRouter} from "vue-router";
import {trans} from "laravel-vue-i18n";
import axios from "axios";

// Komponent-importer
import BackButton from "../../utils/BackButton.vue";
import EventUserPill from "../../utils/EventUserPill.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import AdminEventActionBar from "../../utils/AdminEventActionBar.vue";
import EditButton from "../../utils/EditButton.vue";
import DeleteButton from "../../utils/DeleteButton.vue";
import RecoverButton from "../../utils/RecoverButton.vue";
import PermanentButton from "../../utils/PermanentButton.vue";

// Reactive tilstander
const event = ref(null); // Holder data om event fra API
const loading = ref(true); // Styrer lasteindikatoren
const error = ref(null); // Viser feilmeldinger ved behov

// Router-tilgang
const route = useRoute();
const router = useRouter();

// Funksjoner for å sjekke brukerroller og status
function isCrew(user) {
    return user.roles?.some((role) => role.name.toLowerCase() === "crew");
}

function isInsider(user) {
    if (isCrew(user)) {
        return event.value.insider_crew?.some(u => u.id === user.id) || false;
    }
    return event.value.insider?.some(u => u.id === user.id) || false;
}

function isAttending(user) {
    if (isCrew(user)) {
        return event.value.attending_crew?.some(u => u.id === user.id) || false;
    }
    return event.value.attending?.some(u => u.id === user.id) || false;
}

function isRegistered(user) {
    if (isCrew(user)) {
        return event.value.registered_crew?.some(u => u.id === user.id) || false;
    }
    return event.value.registered?.some(u => u.id === user.id) || false;
}

// Hent event-data fra API
async function getEvent() {
    try {
        const response = await axios.get(`/api/admin/events/${route.params.id}`);
        event.value = response.data;
    } catch (err) {
        error.value = err.message || "En ukjent feil oppstod.";
        console.error("Kunne ikke hente event:", error.value);
    } finally {
        loading.value = false;
    }
}

// Funksjoner for event-handlinger
async function handleStartOver(data) {
    try {
        const response = await axios.post(`/api/admin/events/${event.value.id}/start_over`, data);
        await response;
        getEvent();
    } catch (err) {
        console.error("Feil under handling:", err.message);
    }
}

async function handleSignupEndNow() {
    try {
        const response = await axios.post(`/api/admin/events/${event.value.id}/signup_end_now`);
        await response;
        getEvent();
    } catch (err) {
        console.error("Feil under handling:", err.message);
    }
}

async function handleSignupBeginNow() {
    try {
        const response = await axios.post(`/api/admin/events/${event.value.id}/signup_begin_now`);
        await response;
        getEvent();
    } catch (err) {
        console.error("Feil under handling:", err.message);
    }
}

async function handleEventEndNow() {
    try {
        const response = await axios.post(`/api/admin/events/${event.value.id}/event_end_now`);
        await response;
        getEvent();
    } catch (err) {
        console.error("Feil under handling:", err.message);
    }
}

async function handleEventBeginNow() {
    try {
        const response = await axios.post(`/api/admin/events/${event.value.id}/event_begin_now`);
        await response;
        getEvent();
    } catch (err) {
        console.error("Feil under handling:", err.message);
    }
}

// Oppdater kolonner for brukere
function update({action, user}) {
    const columns = [
        "registered",
        "attending",
        "insider",
        "registered_crew",
        "attending_crew",
        "insider_crew"
    ];

    // Fjern brukeren fra tidligere kolonne
    columns.forEach((col) => {
        event.value[col] = event.value[col]?.filter((u) => u.id !== user.id);
    });

    action = isCrew(user) ? action + "_crew" : action;

    switch (action) {
        case "attend":
            event.value.attending.push(user);
            event.value.insider.push(user);
            break;
        case "attend_crew":
            event.value.attending_crew.push(user);
            event.value.insider_crew.push(user);
            break;
        case "insider":
            event.value.attending.push(user);
            event.value.insider.push(user);
            break;
        case "insider_crew":
            event.value.attending_crew.push(user);
            event.value.insider_crew.push(user);
            break;
        case "unattend":
            event.value.registered.push(user);
            break;
        case "unattend_crew":
            event.value.registered_crew.push(user);
            break;
        case "leave":
            event.value.attending.push(user);
            break;
        case "leave_crew":
            event.value.attending_crew.push(user);
            break;
    }
}

// Reaktive mapped kolonner
const mappedColumns = computed(() => ({
    Registered: event.value?.registered || [],
    Attending: event.value?.attending || [],
    Insider: event.value?.insider || [],
    RegisteredCrew: event.value?.registered_crew || [],
    AttendingCrew: event.value?.attending_crew || [],
    InsiderCrew: event.value?.insider_crew || [],
}));

// Watch for endringer i event
watch(() => event.value, (newVal, oldVal) => {
    console.log("Event endret!", {ny: newVal, gammel: oldVal});
}, {deep: true});

// Initialiser komponenten
onMounted(() => {
    getEvent();
});
</script>
