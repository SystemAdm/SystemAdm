<template>
    <div class="columns-1 md:columns-3 gap-3">
        <div v-for="event in events" class=" mb-3">
            <div class="max-w-sm text-center rounded overflow-hidden shadow-lg bg-gray-400 text-black">
                <img class="flex h-20 place-self-center" :src="image(event.images)" alt="Sunset in the mountains">
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
            axios.get('/api/events/short').then(response => {
                this.events = response.data;
            })
        },
        image(value) {
            return `data:image/png;base64,${value.original}`;
        }
    },
    mounted() {
        this.fetch();
    }
}
</script>
