<template>
    <button
        class="lg:inline-block py-2 px-6 bg-blue-500 hover:bg-blue-600 text-sm text-white font-bold rounded-xl transition duration-200"
        @click="openModal">
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
            <td>{{ event.title }}</td>
            <td>{{ event.event_begin_date }}</td>
            <td>{{ event.event_end_date }}</td>
            <td>{{ event.seats_available }}</td>
        </tr>
        </tbody>
    </table>
    <CreateEvent :modalOpen="modalOpen" @update:created="fetch" @update:modalOpen="modalOpen = $event"/>
</template>
<script>
import axios from "axios";
import CreateEvent from '../modals/CreateEvent.vue';

export default {
    name: "AdminEventsIndex",
    components: {
        CreateEvent
    },
    data() {
        return {
            loading: true,
            modalOpen: false,
            events: []
        }
    },
    methods: {
        fetch() {
            axios.get("/api/admin/events").then((response) => {
                this.events = response.data;
            });
            this.loading = false;
        },
        openModal() {
            this.modalOpen = true;
        }
    },
    mounted() {
        this.fetch();
    }
}
</script>
