<template>
    <div class="container mx-auto px-4 py-8">
        <!-- Loading state -->
        <div v-if="loading" class="flex justify-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
        </div>

        <!-- Events grid -->
        <div v-else-if="events && events.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="event in events" :key="event.id" class="transform transition duration-300 hover:scale-105">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <!-- Image container with fixed aspect ratio -->
                    <div v-if="event.images" class="relative pb-[56.25%]">
                        <img 
                            :src="image(event.images)" 
                            :alt="event.title"
                            class="absolute inset-0 w-full h-full object-cover"
                        >
                    </div>
                    
                    <div class="p-6">
                        <h2 class="font-bold text-xl mb-3 text-gray-800">{{ event.title }}</h2>
                        <p class="text-gray-600 mb-4 line-clamp-3" v-html="event.description"></p>
                        
                        <!-- Date badges -->
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ event.event_begin_date }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ event.event_end_date }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- No events message -->
        <div v-else class="text-center py-8">
            <p class="text-gray-600">Ingen arrangementer funnet</p>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { format } from 'date-fns';
import { nb } from 'date-fns/locale';

export default {
    data() {
        return {
            loading: true,
            events: []
        }
    },
    methods: {
        image(value) {
            // Sjekk om value er et objekt og har 'original' property
            if (value && typeof value === 'object' && value.original) {
                return `data:image/png;base64,${value.original}`;
            }
            // Returner en standard placeholder hvis ingen gyldig verdi
            return ''; // eller en standard placeholder URL
        },
        fetch() {
            axios.get('/api/events')
                .then(response => {
                    this.events = response.data;
                    console.log('Stored events:', this.events);
                })
                .catch(error => {
                    console.error('API error:', error);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        formatDate(dateString) {
            if (!dateString) return '';
            try {
                return format(new Date(dateString), 'dd. MMM yyyy', { locale: nb });
            } catch (error) {
                console.error('Date formatting error:', error);
                return dateString;
            }
        }
    },
    mounted() {
        this.fetch();
    }
}
</script>
