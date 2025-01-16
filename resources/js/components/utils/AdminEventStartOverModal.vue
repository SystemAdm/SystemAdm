<template>
    <div
        v-if="isStartOverModalOpen"
        class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50"
    >
        <!-- Modal Container -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-lg p-6 relative">
            <!-- Title -->
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
                {{ trans('event.SetEventDates') }}
            </h2>

            <!-- Event and Signup Sections -->
            <div class="flex flex-col md:flex-row gap-6 mb-6">
                <!-- Event Section -->
                <div class="flex-1">
                    <p v-if="eventBegin" class="text-sm text-gray-600 dark:text-gray-300 mb-1">
                        Event is set to start at <br/>{{ props.eventBegin }}
                    </p>
                    <label for="sEvent" class="inline-flex items-center mt-2">
                        <input
                            type="checkbox"
                            id="sEvent"
                            name="sEvent"
                            @checked="hasEventBegan"
                            v-model="showEvent"
                            class="form-checkbox h-5 w-5 text-blue-600 border-gray-300 focus:ring-blue-500 dark:border-gray-600 dark:focus:ring-blue-400"
                        />
                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-200">Edit Event</span>
                    </label>
                </div>

                <!-- Signup Section -->
                <div class="flex-1">
                    <p v-if="signupBegin" class="text-sm text-gray-600 dark:text-gray-300 mb-1">
                        Signup is set to start at <br/>{{ props.signupBegin }}
                    </p>
                    <p v-else>Signup is not set</p>
                    <label for="sSignup" class="inline-flex items-center mt-2">
                        <input
                            type="checkbox"
                            id="sSignup"
                            name="sSignup"
                            @checked="hasSignupBegan"
                            v-model="showSignup"
                            class="form-checkbox h-5 w-5 text-blue-600 border-gray-300 focus:ring-blue-500 dark:border-gray-600 dark:focus:ring-blue-400"
                        />
                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-200">Edit Signup</span>
                    </label>
                </div>
            </div>

            <!-- Event Date Inputs -->
            <div v-show="showEvent" class="mb-6">
                <div class="mb-4">
                    <label for="event_begin_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ trans('event.EventBeginDate') }}
                    </label>
                    <input
                        type="datetime-local"
                        id="event_begin_date"
                        v-model="eventBeginDate"
                        required
                        class="form-input"
                    />
                </div>
                <div>
                    <label for="event_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ trans('event.EventEndDate') }}
                    </label>
                    <input
                        type="datetime-local"
                        id="event_end_date"
                        v-model="eventEndDate"
                        required
                        class="form-input"
                    />
                </div>
            </div>

            <!-- Signup Date Inputs -->
            <div v-show="showSignup" class="mb-6">
                <div class="mb-4">
                    <label for="signup_begin_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ trans('event.SignupBeginDate') }}
                    </label>
                    <input
                        type="datetime-local"
                        id="signup_begin_date"
                        v-model="signupBeginDate"
                        required
                        class="form-input"
                    />
                </div>
                <div>
                    <label for="signup_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ trans('event.SignupEndDate') }}
                    </label>
                    <input
                        type="datetime-local"
                        id="signup_end_date"
                        v-model="signupEndDate"
                        required
                        class="form-input"
                    />
                </div>
            </div>

            <!-- Control Buttons -->
            <div class="flex justify-end space-x-4">
                <button
                    @click="closeModal"
                    class="px-4 py-2 bg-gray-200 text-gray-900 rounded-md hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                >
                    {{ trans('Cancel') }}
                </button>
                <button
                    @click="confirmNewDates"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-400"
                >
                    {{ trans('Confirm') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from "vue";
import {trans} from "laravel-vue-i18n";

// Props og emit
const props = defineProps({
    eventBegin: String,
    signupBegin: String,
    hasEventBegan: Boolean,
    hasEventEnded: Boolean,
    hasSignupBegan: Boolean,
    hasSignupEnded: Boolean,
    isStartOverModalOpen: Boolean,
});
const emits = defineEmits([
    "update:isStartOverModalOpen",
    "eventStartOverWithDates",
]);

// Refs for datohåndtering
const eventBeginDate = ref(null);
const eventEndDate = ref(null);
const signupBeginDate = ref(null);
const signupEndDate = ref(null);
const showEvent = ref(false);
const showSignup = ref(false);

// Lukk modal
function closeModal() {
    emits("update:isStartOverModalOpen", false);
}

// Initialisering når komponenten monteres
onMounted(() => {
    eventBeginDate.value = props.eventBegin || null;
    signupBeginDate.value = props.signupBegin || null;
    showEvent.value = props.hasEventBegan;
    showSignup.value = props.hasSignupBegan;
});

// Se etter endringer og reset felt om nødvendig
watch(showEvent, (newValue) => {
    if (!newValue) {
        eventBeginDate.value = null;
        eventEndDate.value = null;
    }
});
watch(showSignup, (newValue) => {
    if (!newValue) {
        signupBeginDate.value = null;
        signupEndDate.value = null;
    }
});

// Valideringslogikk før innsending
function validateDates() {
    if (showEvent.value) {
        if (!eventBeginDate.value || !eventEndDate.value) {
            return trans("event.AllFieldsRequired");
        }
        if (new Date(eventBeginDate.value) >= new Date(eventEndDate.value)) {
            return trans("event.EventBeginMustBeBeforeEventEnd");
        }
    }
    if (showSignup.value) {
        if (!signupBeginDate.value || !signupEndDate.value) {
            return trans("event.AllFieldsRequired");
        }
        if (new Date(signupBeginDate.value) >= new Date(signupEndDate.value)) {
            return trans("event.SignupBeginMustBeBeforeSignupEnd");
        }
    }
    if (showEvent.value && showSignup.value) {
        if (new Date(signupEndDate.value) >= new Date(eventEndDate.value)) {
            return trans("event.SignupEndMustBeBeforeEventEnd");
        }
        if (new Date(signupBeginDate.value) >= new Date(eventBeginDate.value)) {
            return trans("event.SignupBeginMustBeBeforeEventBegin");
        }
    }
    return null; // Ingen feil
}

// Bekreft nye datoer
function confirmNewDates() {
    const validationError = validateDates();
    if (validationError) {
        alert(validationError);
        return;
    }

    // Emit validerte data
    emits("eventStartOverWithDates", {
        eventBegin: eventBeginDate.value,
        eventEnd: eventEndDate.value,
        signupBegin: signupBeginDate.value,
        signupEnd: signupEndDate.value,
        showEvent: showEvent.value,
        showSignup: showSignup.value,
    });
    closeModal();
}
</script>
