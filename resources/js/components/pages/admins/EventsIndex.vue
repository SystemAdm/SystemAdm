<template>
    <div>
        <!-- Hovedoverskrift -->
        <div>
            <h1 class="text-2xl font-bold mb-4">{{ trans('events.AdminIndex') }}</h1>

            <!-- Loader -->
            <div v-if="loading" class="text-center">
                <div class="loader"></div>
                <p>Laster data...</p>
            </div>

            <!-- Event-fliser -->
            <div v-else>
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
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">{{ event.title }}</h2>

                            <!-- Event Beskrivelse -->
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">{{ event.description }}</p>

                            <!-- Statusseksjon -->
                            <div class="flex items-center space-x-4 mt-2 text-gray-600 dark:text-gray-400">
                                <!-- Event start/slutt status -->
                                <div>
                                    <font-awesome-icon
                                        v-if="event.event_ended"
                                        :icon="['fas', 'flag-checkered']"
                                        class="text-red-500"
                                        :title="'Eventet er avsluttet'"
                                    />
                                    <font-awesome-icon
                                        v-else-if="event.event_began && !event.event_ended"
                                        :icon="['fas', 'play-circle']"
                                        class="text-green-500"
                                        :title="'Eventet er startet, men ikke avsluttet'"
                                    />
                                    <font-awesome-icon
                                        v-else-if="!event.event_began"
                                        :icon="['fas', 'clock']"
                                        class="text-yellow-500"
                                        :title="'Eventet har ikke startet'"
                                    />
                                </div>

                                <!-- Påmeldingsstatus -->
                                <div>
                                    <font-awesome-icon
                                        v-if="event.signup_ended"
                                        :icon="['fas', 'times-circle']"
                                        class="text-red-500"
                                        :title="'Påmeldingen til eventet er avsluttet'"
                                    />
                                    <font-awesome-icon
                                        v-else-if="event.signup_began && !event.signup_ended"
                                        :icon="['fas', 'check-circle']"
                                        class="text-green-500"
                                        :title="'Påmeldingen er åpen'"
                                    />
                                    <font-awesome-icon
                                        v-else-if="!event.signup_began"
                                        :icon="['fas', 'calendar-plus']"
                                        class="text-yellow-500"
                                        :title="'Påmeldingen har ikke startet enda'"
                                    />
                                </div>
                            </div>

                            <template v-if="event.duration_days === 0">
                                <p>
                                    <strong>Dato:</strong> {{ event.event_begin_date }}
                                </p>
                                <p>
                                    <strong>Tid:</strong> {{ event.event_begin_time }} - {{ event.event_end_time }}
                                </p>
                                <p>
                                    <strong>Varighet:</strong> {{ event.duration_hours }} timer
                                </p>
                            </template>

                            <!-- Hvis eventet varer over flere dager -->
                            <template v-else>
                                <p>
                                    <strong>Start:</strong> {{ event.event_begin_date }} kl. {{ event.event_begin_time }}
                                </p>
                                <p>
                                    <strong>Varighet:</strong> {{ event.duration_days }} dager og {{ event.duration_hours }} timer
                                </p>
                                <p>
                                    <strong>Slutt:</strong> {{ event.event_end_date }} kl. {{ event.event_end_time }}
                                </p>
                            </template>

                            <!-- Lokasjon -->
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                <strong>Sted:</strong> <router-link :to="'/admins/locations/'+event.location.id">{{ event.location.name }}</router-link>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Ingen events melding -->
                <div v-else>
                    <p class="text-center text-gray-600 dark:text-gray-400">Ingen eventer å vise.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { trans } from "laravel-vue-i18n";
import { onMounted, ref } from "vue";
import axios from "axios";

// Font Awesome
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
const events = ref([]); // Start med tom liste
const loading = ref(true); // Laster som standard

async function getEvents() {
    try {
        const response = await axios.get("/api/admin/events");
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
