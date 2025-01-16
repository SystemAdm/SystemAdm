<template>
    <div class="text-gray-800 dark:text-gray-200">
        <h1 class="text-3xl font-bold mb-4">{{ trans('events.Index') }}</h1>
        <BackButton @goBack="router.back()"/>
        <!-- Lastingindikator -->
        <div v-if="loading" class="text-center">
            <p>
                <font-awesome-icon icon="fa-spinner" spin class="mr-2"/>
                {{ trans('Loading...') }}
            </p>
        </div>
        <!-- Feilmelding -->
        <div v-if="error" class="text-red-500 text-center my-4">
            {{ trans('error_loading') }}: {{ error }}
        </div>

        <!-- Event-fliser -->
        <div v-if="!loading && events.length > 0"
             class="text-center table-auto w-full rounded-lg shadow-md overflow-hidden bg-white dark:bg-gray-800 dark:border-gray-700 hover:shadow-lg transition duration-200 p-2">
            <div class="table-header-group">
                <div class="table-row">
                    <div class="table-cell row-span-2">ID</div>
                    <div class="table-cell row-span-2">R & E</div>
                    <div class="table-cell">Title</div>
                    <div class="table-cell">Users</div>
                    <div class="table-cell">Crew</div>
                </div>
                <div class="table-row border border-b-2 border-gray-200">
                    <div class="table-cell"></div>
                    <div class="table-cell"></div>
                    <div class="table-cell"></div>
                    <div class="table-cell"><abbr title="Registered">R</abbr> / <abbr title="Attending">A</abbr> / <abbr
                        title="Inside">I</abbr></div>
                    <div class="table-cell"><abbr title="Registered">R</abbr> / <abbr title="Attending">A</abbr> / <abbr
                        title="Inside">I</abbr></div>
                </div>
            </div>
            <div
                v-for="event in events"
                :key="event.id"
                class="table-row border rounded-lg shadow-md overflow-hidden bg-white dark:bg-gray-800 dark:border-gray-700 hover:shadow-lg transition duration-200 cursor-pointer"
                @click="router.push('/admins/events/' + event.id)"
            >
                <div class="table-cell px-1">{{ event.id }}</div>
                <div class="table-cell items-center text-gray-600 dark:text-gray-400">
                    <!-- Påmeldingsstatus -->
                    <div class=" px-1" >
                        <span v-if="event.registration_needed && event.signup_begin !== null">
                        <font-awesome-icon
                            v-if="event.signup_ended" class="text-red-500 mr-1"
                            :icon="['fas', 'times-circle']"
                            :title="trans('events.SignupEnded')"
                        />
                        <font-awesome-icon
                            v-else-if="event.signup_began && !event.signup_ended" class="text-green-500 mr-1"
                            :icon="['fas', 'check-circle']"
                            :title="trans('events.SignupStarted')"
                        />
                        <font-awesome-icon
                            v-else-if="!event.signup_began" class="text-yellow-500 mr-1"
                            :icon="['fas', 'calendar-plus']"
                            :title="trans('events.SignupNotStarted')"
                        />
                            </span>
                        <!-- Event start/slutt status -->
                        <font-awesome-icon
                            v-if="event.event_ended" class="text-red-500 mr-1"
                            :icon="['fas', 'flag-checkered']"
                            :title="trans('events.EventEnded')"
                        />
                        <font-awesome-icon
                            v-else-if="event.event_began && !event.event_ended" class="text-green-500 mr-1"
                            :icon="['fas', 'play-circle']"
                            :title="trans('events.EventStarted')"
                        />
                        <font-awesome-icon
                            v-else-if="!event.event_began" class="text-yellow-500 mr-1"
                            :icon="['fas', 'clock']"
                            :title="trans('events.EventNotStarted')"
                        />
                    </div>

                </div>
                <span class="table-cell px-1">{{ event.title }}</span>
                <div class="table-cell px-2">{{ event.registered.length }} / {{ event.attending.length }} /
                    {{ event.insider.length }}
                </div>
                <div class="table-cell px-2">{{ event.registered_crew.length }} / {{ event.attending_crew.length }} /
                    {{ event.insider_crew.length }}
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
import BackButton from "../../utils/BackButton.vue";

const events = ref([]); // Start med tom liste
const loading = ref(true); // Laster som standard
const error = ref(null);
const router = useRouter();

async function getEvents() {
    loading.value = true;
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
