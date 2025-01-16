<template>
    <div v-if="event.signup_began !== null && event.signup_ended !== null" class="space-x-4 inline-flex">
        <!-- Signup Status -->
        <div v-bind:class="'mb-4 btn-group '">
            <button :class="'btn ' + signupStatus.buttonClass" disabled>{{ signupStatus.buttonText }}</button>
            <button class="btn btn-primary" @click="triggerAction(signupStatus.action)">{{ signupStatus.actionText }}</button>
        </div>

        <!-- Event Status -->
        <div v-bind:class="'mb-4 btn-group '">
            <button :class="'btn '+eventStatus.buttonClass" disabled>{{ eventStatus.buttonText }}</button>
            <button class="btn btn-primary" @click="triggerAction(eventStatus.action)">{{ eventStatus.actionText }}</button>
        </div>
    </div>

    <!-- Modal -->
    <AdminEventStartOverModal
        :isStartOverModalOpen="isStartOverModalOpen"
        :hasEventBegan="props.event.event_began"
        :hasEventEnded="props.event.event_ended"
        :hasSignupBegan="props.event.signup_began || props.event.signup_begin === null"
        :hasSignupEnded="props.event.signup_ended"
        :eventBegin="props.event.event_begin_date"
        :signupBegin="props.event.signup_begin_date"
        @update:isStartOverModalOpen="isStartOverModalOpen = $event"
        @eventStartOverWithDates="handleStartOverWithDates"
    />
</template>

<script setup>
import { trans } from "laravel-vue-i18n";
import AdminEventStartOverModal from "../utils/AdminEventStartOverModal.vue";
import { ref, watch, computed } from "vue";

const getSignupStatus = (event) => {
    if (event.signup_ended) {
        return {
            status: "ended",
            buttonClass: "btn-danger",
            buttonText: trans("events.SignupHasEnded"),
            actionText: trans("events.SignupStartOver"),
            action: "startOver",
        };
    } else if (event.signup_began) {
        return {
            status: "ongoing",
            buttonClass: "btn-warning",
            buttonText: trans("events.SignupOngoing"),
            actionText: trans("events.SignupEndNow"),
            action: "signupEndNow",
        };
    }
    return {
        status: "not_began",
        buttonClass: "btn-warning",
        buttonText: trans("events.SignupNotBegan"),
        actionText: trans("events.SignupBeginNow"),
        action: "signupBeginNow",
    };
};

const getEventStatus = (event) => {
    if (event.event_ended) {
        return {
            status: "ended",
            buttonClass: "btn-danger",
            buttonText: trans("events.EventHasEnded"),
            actionText: trans("events.EventStartOver"),
            action: "startOver",
        };
    } else if (event.event_began) {
        return {
            status: "ongoing",
            buttonClass: "btn-warning",
            buttonText: trans("events.EventOngoing"),
            actionText: trans("events.EventEndNow"),
            action: "eventEndNow",
        };
    }
    return {
        status: "not_began",
        buttonClass: "btn-warning",
        buttonText: trans("events.EventNotBegan"),
        actionText: trans("events.EventBeginNow"),
        action: "eventBeginNow",
    };
};

const isStartOverModalOpen = ref(false);
const props = defineProps({ event: Object });
const emits = defineEmits(["startOver", "signupEndNow", "signupBeginNow", "eventEndNow", "eventBeginNow"]);

// Hent dynamiske statusobjekter
const signupStatus = computed(() => getSignupStatus(props.event));
const eventStatus = computed(() => getEventStatus(props.event));

// Handling av ulike actions
function triggerAction(action) {
    if (action === "startOver") {
        isStartOverModalOpen.value = true;
    } else {
        const confirmation = confirm(trans(`events.${action}Confirm`));
        if (!confirmation) return;
        emits(action);
    }
}

function handleStartOverWithDates(data) {
    emits("eventStartOver", data);
}
</script>
