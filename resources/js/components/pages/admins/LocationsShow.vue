<template>
    <div>
        <button @click="$emit('goBack')">
            {{ trans('Tilbake') }}
        </button>

        <div v-if="loading">{{ trans('Laster lokasjon...') }}</div>
        <div v-if="error">{{ trans('En feil oppsto under henting av lokasjon:') }} {{ error }}</div>
        <div v-if="location">
            <h1>{{ location.name }}</h1>
            <p>{{ trans('Adresse:') }} {{ location.street_name }} {{ location.street_number }}</p>

            <!-- Bruker Google Maps Iframe -->
            <iframe
                :src="googleMapUrl"
                width="100%"
                height="400"
                style="border: 0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
        </div>
    </div>
</template>

<script setup>
import {ref, computed, onMounted} from "vue";
import {trans} from "laravel-vue-i18n";
import axios from "axios";
import {useRoute} from "vue-router";

// Definer variabler
const location = ref(null);
const loading = ref(true);
const error = ref(null);

const route = useRoute();

async function fetchLocation() {
    try {
        const response = await axios.get("/api/admin/locations/"+route.params.id);
        location.value = response.data;
    } catch (err) {
        error.value = err.message;
    } finally {
        loading.value = false;
    }
}

// Dynamisk Google Maps Iframe URL
const googleMapUrl = computed(() => {
    if (location.value?.lat && location.value?.lng) {
        const {lat, lng} = location.value;
        return `https://www.google.com/maps/embed/v1/view?key=AIzaSyCD7fSJbjbZBvT11pynJjhFnDXe0rT4DfQ&center=${lat},${lng}&zoom=12`;
    }
    return "";
});

onMounted(fetchLocation);
</script>
