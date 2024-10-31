<template>
    <div class="columns-3 gap-3">
        <div v-for="event in events" class=" mb-3">
            <div class="max-w-sm rounded overflow-hidden shadow-lg bg-gray-400 text-black">
                <img class="w-full h-20" :src="event.images" alt="Sunset in the mountains">
                <div class="px-6 py-4">
                    <div class="font-bold text-xl mb-2">{{ event.title }}</div>
                    <p class="text-gray-700 text-base" v-html="event.description">
                    </p>
                </div>
                <div class="px-6 pt-4 pb-2">
                    <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">{{ event.event_begin_date }}</span>
                    <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">{{ event.event_end_date}}</span>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from "axios";
export default {
    data(){
        return {
            loading:true,
            events: null
        }
    },
    methods: {
        fetch() {
            axios.get('/api/events').then(response => {
                this.events = response.data;
            })
        }
    },
    mounted() {
        this.fetch();
    }
}
</script>
