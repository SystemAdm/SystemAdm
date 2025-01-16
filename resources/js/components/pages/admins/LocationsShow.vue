<template>
    <div class="text-gray-800 dark:text-gray-200">
         <h1 class="text-3xl font-bold mb-4">{{ trans('locations.title') }}</h1>
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
        <!-- Lokasjon finnes -->
        <div v-if="location">
            <div
                class="border rounded-lg shadow-md overflow-hidden bg-white dark:bg-gray-800 dark:border-gray-700 hover:shadow-lg transition duration-200 text-center"
            >
                <!-- Bildet som header -->
                <div v-if="location.images?.original" class="h-48 w-full relative">
                    <img
                        :src="'data:image/png;base64,' + location.images.original"
                        :alt="location.name"
                        class="w-full h-full object-cover"
                    />
                </div>
                <!-- Lokasjonsnavn -->
                <h1 class="text-2xl font-bold my-2">{{ location.name }}</h1>
                <div v-if="location.email">
                    <!-- E-post -->
                    <p>
                        <strong>{{ trans('E-mail address') }}:</strong>
                        <a
                            :href="'mailto:' + location.email.full_email"
                            target="_blank"
                            class="hover:underline visited:text-purple-800 text-blue-500 ml-2"
                        >
                            {{ location.email.full_email }}
                        </a>
                    </p>
                </div>
                <!-- Postadresse -->
                <div v-if="location.postal" class="pb-5">
                    <p>
                        <strong>{{ trans('locations.street-address') }}:</strong>
                        {{ location.street_name }} {{ location.street_number }}
                    </p>
                    <p>
                        <strong>{{ trans('locations.postal') }}:</strong>
                        {{ location.postal.code }} {{ location.postal.name }}
                    </p>
                    <p>
                        <strong>{{ trans('locations.municipality') }}:</strong>
                        {{ location.postal.municipality }}
                    </p>
                    <p>
                        <strong>{{ trans('locations.county') }}:</strong>
                        {{ location.postal.county }}
                    </p>
                    <p>
                        <strong>{{ trans('locations.country') }}:</strong>
                        {{ location.postal.country }}
                    </p>
                </div>
                <!-- Google Maps Iframe -->
                <GoogleMap v-if="location.lat && location.lng && location.zoom"
                    api-key="AIzaSyCD7fSJbjbZBvT11pynJjhFnDXe0rT4DfQ"
                    style="width: 100%; height: 300px"
                    :center="center"
                    :zoom="location.zoom"
                >
                    <Marker :options="{ position: center }" />
                </GoogleMap>

                <!-- Alternativ lenke -->
                <div v-if="location.id === 0">
                    <p>
                        <a href="https://www.spillhuset.com" class="text-blue-500 hover:underline">
                            www.spillhuset.com
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {computed, onMounted, ref} from "vue";
import {trans} from "laravel-vue-i18n";
import axios from "axios";
import {useRoute, useRouter} from "vue-router";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

// Definer variabler
const route = useRoute();
const router = useRouter();
const location = ref(null);
const loading = ref(true);
const error = ref(null);

// Hent lokasjonsdata fra API-et
async function fetchLocation() {
    try {
        const response = await axios.get(`/api/admin/locations/${route.params.id}`);
        location.value = response.data;
    } catch (err) {
        console.error("Feil under lasting av lokasjon:", err);
        error.value = err.message || trans("An unknown error occurred.");
    } finally {
        loading.value = false;
    }
}

import { GoogleMap, Marker } from 'vue3-google-map'
import BackButton from "../../utils/BackButton.vue";

const center = computed(() => {
    if (location.value?.lat && location.value?.lng) {
        return {lat: location.value.lat, lng: location.value.lng}
    }
    return null;
});

onMounted(fetchLocation);
</script>
