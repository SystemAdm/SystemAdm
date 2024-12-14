<script setup>
import {onMounted, ref} from 'vue';
import axios from "axios";
import CreateEvent from "../modals/CreateEvent.vue";

// Reactive state
const events = ref([]);
const modalOpen = ref(false);
const loading = ref(true);

const props = defineProps({
    me:{
        type:Object,
    },
});

// Function to fetch events from the API
const fetch = async () => {
    try {
        const response = await axios.get("/api/admin/events");
        events.value = response.data;
    } catch (error) {
        console.error("Failed to fetch events:", error);
    } finally {
        loading.value = false;
    }
};

// Function to compute status with font-awesome icons
const computeStatus = (began, ended) => {
    if (!began) {
        return {icon: "circle-notch", color: "text-gray-400"};
    }
    if (began && !ended) {
        return {icon: "play-circle", color: "text-green-500"};
    }
    if (ended) {
        return {icon: "stop-circle", color: "text-red-500"};
    }
};

// Run fetch on component mount
onMounted(fetch);
</script>
<template>
    <button
        class="lg:inline-block py-2 px-6 bg-blue-500 hover:bg-blue-600 text-sm text-white font-bold rounded-xl transition duration-200"
        @click="modalOpen = true">
        New Event
    </button>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>#id</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="event in events" :key="event.id">
            <td>{{ event.id }}</td>
            <td>
                <font-awesome-icon :icon="['fas',computeStatus(event.signup_began, event.signup_ended).icon]" :class="computeStatus(event.signup_began, event.signup_ended).color"/>
            </td>
            <td><font-awesome-icon :icon="['fas',computeStatus(event.event_began, event.event_ended).icon]" :class="computeStatus(event.event_began, event.event_ended).color"/></td>
            <td>{{ event.title }}</td>
            <td>{{ event.event_begin_date }}</td>
            <td>{{ event.event_end_date }}</td>
            <td>{{ event.seats_available }}</td>
        </tr>
        </tbody>
    </table>
    <CreateEvent :modalOpen="modalOpen" @update:created="fetch" @update:modalOpen="modalOpen = !modalOpen"/>
</template>