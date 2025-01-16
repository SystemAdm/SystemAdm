<template>
    <div class="text-gray-800 dark:text-gray-200">
        <h1 class="text-3xl font-bold mb-4">{{ trans('events.Index') }}</h1>
        <BackButton @goBack="router.back()" />
        <!-- Lastingindikator -->
        <div v-if="loading" class="text-center">
            <p>
                <font-awesome-icon icon="fa-spinner" spin class="mr-2" />
                {{ trans('Loading...') }}
            </p>
        </div>
        <!-- Feilmelding -->
        <div v-if="error" class="text-red-500 text-center my-4">
            {{ trans('error_loading') }}: {{ error }}
        </div>

        <!-- Event-fliser -->
        <div v-if="events.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="event in events"
                :key="event.id"
                class="border rounded-lg shadow-md overflow-hidden bg-white dark:bg-gray-800 dark:border-gray-700 hover:shadow-lg transition duration-200"
            >
                <!-- Bildet som header -->
                <div v-if="event.images && event.images.original" class="h-48 w-full relative">
                    <img
                        :src="'data:image/png;base64,' + event.images.original"
                        :alt="event.title"
                        class="w-full h-full object-cover"
                    />
                </div>

                <!-- Flisinnhold -->
                <div class="p-4">
                    <!-- Event Tittel -->
                    <h2 class="text-xl font-semibold mb-2">{{ event.title }}</h2>

                    <!-- Event Beskrivelse -->
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">{{ event.description }}</p>

                    <!-- Statusseksjon -->
                    <div class="flex items-center space-x-4 my-2 text-gray-600 dark:text-gray-400">
                        <!-- Påmeldingsstatus -->
                        <div v-if="event.signup_ended" class="text-red-500">
                            <font-awesome-icon
                                :icon="['fas', 'times-circle']"
                                class=" mr-1"
                                :title="trans('events.SignupEnded')"
                            />
                            <span>{{trans('events.SignupEnded')}}</span>
                        </div>
                        <div v-else-if="event.signup_began && !event.signup_ended" class="text-green-500">
                            <font-awesome-icon
                                :icon="['fas', 'check-circle']"
                                class=" mr-1"
                                :title="trans('events.SignupStarted')"
                            />
                            <span>{{trans('events.SignupStarted')}}</span>
                        </div>
                        <div v-else-if="!event.signup_began" class="text-yellow-500">
                            <font-awesome-icon
                                :icon="['fas', 'calendar-plus']"
                                class=" mr-1"
                                :title="trans('events.SignupNotStarted')"
                            />
                            <span>{{trans('events.SignupNotStarted')}}</span>
                        </div>

                        <!-- Event start/slutt status -->
                        <div v-if="event.event_ended" class="text-red-500">
                            <font-awesome-icon
                                :icon="['fas', 'flag-checkered']"
                                class=" mr-1"
                                :title="trans('events.EventEnded')"
                            />
                            <span>{{ trans('events.EventEnded') }}</span>
                        </div>
                        <div v-else-if="event.event_began && !event.event_ended" class="text-green-500">
                            <font-awesome-icon
                                :icon="['fas', 'play-circle']"
                                class=" mr-1"
                                :title="trans('events.EventStarted')"
                            />
                            <span>{{trans('events.EventStarted')}}</span>
                        </div>
                        <div v-else-if="!event.event_began" class="text-yellow-500">
                            <font-awesome-icon
                                :icon="['fas', 'clock']"
                                class=" mr-1"
                                :title="trans('events.EventNotStarted')"
                            />
                            <span>{{trans('events.EventNotStarted')}}</span>
                        </div>
                    </div>

                    <template v-if="event.duration_days === 0">
                        <p>
                            <strong>{{ trans('Date')}}</strong>: {{ event.event_begin_date }}
                        </p>
                        <p>
                            <strong>{{trans('Time')}}</strong>: {{ event.event_begin_time }} - {{ event.event_end_time }}
                        </p>
                        <p>
                            <strong>{{trans('events.Duration')}}</strong>: {{ event.duration_hours }} {{trans('events.hours')}}
                        </p>
                    </template>

                    <!-- Hvis eventet varer over flere dager -->
                    <template v-else>
                        <p>
                            <strong>{{trans('events.Begin')}}</strong>: {{ event.event_begin_date }} {{ trans('events.kl') }}. {{ event.event_begin_time }}
                        </p>
                        <p>
                            <strong>{{trans('events.Duration')}}</strong>: {{ event.duration_days }} {{ trans('events.Days') }} {{trans('And')}} {{ event.duration_hours }}
                            {{trans('events.hours')}}
                        </p>
                        <p>
                            <strong>{{ trans('events.End') }}</strong>: {{ event.event_end_date }} {{ trans('events.kl') }}. {{ event.event_end_time }}
                        </p>
                    </template>

                    <!-- Lokasjon -->
                    <p>
                        <strong>{{trans('events.Location')}}</strong>:
                        <router-link :to="'/locations/'+event.location.id"
                                     class="hover:underline  text-blue-500">{{
                                event.location.name
                            }}
                        </router-link>
                    </p>
                </div>
            </div>
        </div>

        <!-- Ingen events melding -->
        <div v-else-if="!loading">
            <p class="text-center text-gray-600 dark:text-gray-400">{{ trans('events.NoEvents') }}.</p>
        </div>
    </div>
</template>

<script setup>
import {trans} from "laravel-vue-i18n";
import {onMounted, ref} from "vue";
import axios from "axios";
import {useRouter} from 'vue-router';

// Font Awesome
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import BackButton from "../utils/BackButton.vue";

const events = ref([]); // Start med tom liste
const loading = ref(true); // Laster som standard
const error = ref(null);
const router = useRouter();

async function getEvents() {
    try {
        const response = await axios.get("/api/events");
        events.value = response.data;
    } catch (error) {
        console.error("Kunne ikke hente events: ", error);
    } finally {
        loading.value = false; // Slutt på lasting, uansett hva som skjer
    }
}

onMounted(() => {
    getEvents(); // Hent events når komponenten monteres
});
</script>
