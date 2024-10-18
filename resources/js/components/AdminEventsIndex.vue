<template>
    <button
        class="lg:inline-block py-2 px-6 bg-blue-500 hover:bg-blue-600 text-sm text-white font-bold rounded-xl transition duration-200" @click="openModal">
        New Event
    </button>
    <table>
        <thead>
        <tr>
            <th>#id</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="event in events" :key="event.id">
            <td>{{event.id}}</td>
        </tr>
        </tbody>
    </table>
    <ModalComponent v-model="showModal" />
</template>
<script>
import axios from "axios";
import ModalComponent from './CreateEventModal.vue';
export default {
    name: "AdminEventsIndex",
    components: {
        ModalComponent
    },
    data() {
        return {
            loading: true,
            showModal: false,
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
            this.showModal = true;
        }
    },
    mounted() {
        this.fetch();
    }
}
</script>
