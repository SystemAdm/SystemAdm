<template>
    <div v-if="loading">{{ $t('common.loading') }}</div>
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 w-full">
        <div 
            v-for="event in events" 
            :key="event.id"
            class="relative mx-auto w-full p-5"
        >
            <a href="#"
               class="relative inline-block duration-300 ease-in-out transition-transform transform hover:-translate-y-2 w-full">
                <div class="shadow rounded-lg bg-gray-300"
                     :class="{'bg-yellow-200':event.private,'bg-blue-200':event.protected,'bg-red-200':event.cancelled,'bg-orange-200':event.planning}">
                    <div class="flex justify-center relative rounded-lg overflow-hidden h-52">
                        <div class="transition-transform duration-500 transform ease-in-out hover:scale-110 w-full">
                            <div class="absolute">
                                <img :src="image(event.images)" alt="Tesy">
                            </div>
                        </div>

                        <div class="absolute flex justify-center bottom-0 mb-3">
                            <div class="flex bg-white px-4 py-1 space-x-5 rounded-lg overflow-hidden shadow">
                                <span class="text-black font-bold">{{ event.title }}</span>
                            </div>
                        </div>

                        <span v-if="event.private"
                              class="absolute top-0 left-0 inline-flex px-3 py-2 rounded-br-lg z-10 bg-yellow-600 text-sm font-medium text-white select-none uppercase">
                            {{ $t('events.crew_only') }}
                        </span>
                        <span v-else-if="event.protected"
                              class="absolute top-0 left-0 inline-flex px-3 py-2 rounded-lg z-10 bg-blue-600 text-sm font-medium text-white select-none uppercase">
                            {{ $t('events.members_only') }}
                        </span>
                        <span v-if="event.rating === 'esrb'"
                              class="absolute top-0 right-0 inline-flex"><a href="https://www.esrb.org/ratings-guide/" target="_blank"><img
                            :src="'/images/esrb/'+esrb[event.rating_min]" :alt="event.rating_min" style="height: 50px"></a></span>
                        <span v-if="event.rating === 'pegi'"
                              class="absolute top-0 right-0 inline-flex"><a href="https://pegi.info/what-do-the-labels-mean" target="_blank"><img
                            :src="'/images/pegi/'+pegi[event.rating_min]" :alt="event.rating_min" style="height: 50px"></a></span>
                        <div class="absolute right-0">
                            <span v-if="event.rating === 'age' && event.rating_max != null" 
                                  class="block px-3 pt-2 text-right bg-blue-600 text-sm font-medium text-white select-none uppercase">
                                {{ $t('events.age_max', { age: event.rating_max }) }}
                            </span>
                            <span v-if="event.rating === 'age' && event.rating_min != null" 
                                  class="block px-3 py-2 text-right rounded-bl-lg bg-blue-600 text-sm font-medium text-white select-none uppercase">
                                {{ $t('events.age_min', { age: event.rating_min }) }}
                            </span>
                            <span v-if="event.rating === 'grade' && event.rating_max != null" 
                                  class="block px-3 pt-2 text-right bg-blue-600 text-sm font-medium text-white select-none uppercase">
                                {{ $t('events.grade_max', { grade: event.rating_max }) }}
                            </span>
                            <span v-if="event.rating === 'grade' && event.rating_min != null" 
                                  class="block px-3 py-2 text-right rounded-bl-lg bg-blue-600 text-sm font-medium text-white select-none uppercase">
                                {{ $t('events.grade_min', { grade: event.rating_min }) }}
                            </span>
                        </div>
                    </div>

                    <div class=" px-4 py-2">
                        <h2 class="font-bold text-base md:text-lg text-gray-800 line-clamp-1">
                            {{ event.location.name }}
                        </h2>
                        <p class="text-sm text-gray-800 line-clamp-1">
                            {{ event.location.street_name }} {{ event.location.street_number }},
                            {{ event.location.postal_id }}
                        </p>
                    </div>

                    <div v-if="event.duration_days > 0" class="grid gap-2 p-4">
                        <p class="inline-flex flex-col xl:flex-row xl:items-center text-gray-800">
                            <span class="font-medium">{{ $t('events.event_begin') }}:</span>
                            <span class="xl:mt-0">{{ event.event_begin_date }}</span>
                        </p>
                        <p class="inline-flex flex-col xl:flex-row xl:items-center text-gray-800">
                            <span class="font-medium">{{ $t('events.event_end') }}:</span>
                            <span class="xl:mt-0">{{ event.event_end_date }}</span>
                        </p>
                    </div>
                    <div v-else class="grid gap-2 p-4">
                        <p class="inline-flex flex-col xl:flex-row xl:items-center text-gray-800">
                            <span class="font-medium">{{ $t('events.date') }}:</span>
                            <span class="xl:mt-0">{{ event.event_date }}</span>
                        </p>
                        <p class="inline-flex flex-col xl:flex-row xl:items-center text-gray-800">
                            <span class="font-medium">{{ $t('events.time') }}:</span>
                            <span class="xl:mt-0">{{ event.event_time_start }} - {{ event.event_time_end }}</span>
                        </p>
                    </div>

                    <div class="p-4">
                        <div v-if="event.cancelled">
                            <div class="hover:cursor-default block rounded-2xl py-2 px-3 mb-2 text-center bg-red-600">
                                {{ $t('events.cancelled') }}:<span class="block font-extrabold">{{ event.cancelled_text }}</span>
                            </div>
                        </div>
                        <div v-else-if="event.planning">
                            <div class="hover:cursor-default block rounded-2xl py-2 px-3 mb-2 text-center bg-yellow-600">
                                {{ $t('events.status') }}:<span class="block font-extrabold">{{ $t('events.planning_mode') }}</span>
                            </div>
                        </div>
                        <div v-else>
                            <div v-if="event.seats_available !== false && event.seats_available !== true">
                                <div :class="{'bg-red-500':event.seats_available <= 0}"
                                     class="hover:cursor-default block rounded-2xl py-2 px-3 mb-2 text-center bg-green-600">
                                    {{ $t('events.seats_available', { available: event.seats_available, total: event.seats }) }}
                                </div>
                            </div>

                            <div v-if="event.seats_available === false">
                                <div class="hover:cursor-default block rounded-2xl py-2 px-3 mb-2 text-center bg-yellow-600">
                                    {{ $t('events.registration_not_needed') }}
                                </div>
                            </div>
                            <div v-if="event.seats_available === true">
                                <div class="hover:cursor-default block rounded-2xl py-2 px-3 mb-2 text-center bg-green-600">
                                    {{ $t('events.unlimited_seats') }}
                                </div>
                            </div>
                            <div v-if="event.seats_available !== false">
                                <button
                                    :disabled="event.seats_available <= 0 || event.planning"
                                    class="disabled:bg-red-500 disabled:text-gray-400 block w-full py-2 px-6 bg-blue-500 hover:bg-blue-600 text-sm text-white font-bold rounded-xl transition duration-200"
                                    v-on:click="signup(event.id)">
                                    {{ $t('events.sign_me_up') }}
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </a>
        </div>
    </div>
    <EventSignup 
        :modalOpen="modalOpen" 
        @update:modalOpen="modalOpen = $event" 
        :selected="selected"
    />
</template>
<script setup>
import { ref, onMounted } from 'vue';
import axios from "axios";
import EventSignup from "../components/modals/EventSignup.vue";

const pegi = {
    3: 'age-3.jpg',
    7: 'age-7.jpg',
    12: 'age-12.jpg',
    16: 'age-16.jpg',
    18: 'age-18.jpg'
};

const esrb = {
    3: 'E.svg',
    10: 'E10plus.svg',
    13: 'T.svg',
    17: 'M.svg',
    18: 'AO.svg'
};

const loading = ref(true);
const events = ref([]);
const selected = ref(0);
const modalOpen = ref(false);

const fetch = async () => {
    try {
        const response = await axios.get('/api/events/short');
        loading.value = false;
        events.value = response.data;
    } catch (error) {
        console.error('Error fetching events:', error.data);
    }
};

const signup = (id) => {
    modalOpen.value = true;
    selected.value = id;
};

const image = (value) => {
    return `data:image/png;base64,${value.original}`;
};

onMounted(() => {
    fetch();
});
</script>
