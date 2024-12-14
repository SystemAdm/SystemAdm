<template>
    <div class="p-4 mx-auto" v-if="props.modalOpen.value">
        <h1 class="text-2xl font-semibold mb-4">
            {{ trans('create.CreateEvent') }}
        </h1>

        <div class="flex space-x-2 mb-6">
            <div v-for="(stepName,index) in steps" :key="index"
                 :class="['flex-1 py-2 text-center rounded border', step === index+1?'bg-blue-500 text-white':'bg-gray-200 text-gray-700']">
                {{ index + 1 }}. {{ trans('create.' + stepName) }}
            </div>
        </div>
        <component :is="currentComponent" v-model="eventData" :error="error" :step="step" @next-step="handleNext"
                   @prev-step="handlePrev"/>
        <div class="flex items-center justify-between mt-6">
            <button
                v-if="step > 1"
                class="bg-gray-300 text-gray-700 px-4 py-2 rounded"
                @click="handlePrev"
            >
                Forrige
            </button>

            <button
                v-if="step < steps.length"
                class="bg-blue-500 text-white px-4 py-2 rounded"
                @click="handleNext"
            >
                Neste
            </button>

            <button
                v-else
                class="bg-green-500 text-white px-4 py-2 rounded"
                @click="submitEvent"
            >
                Fullfør
            </button>
        </div>
    </div>
</template>

<script setup>

import {trans} from "laravel-vue-i18n";
import {computed, ref} from "vue";

const step = ref(1);
const steps = [
    'basic','settings','requirements','upload','location','reservations'
];
const eventData = ref({
    name: '',
    description: '',
    event_start_date: '',
    event_end_date: '',
    signup_start_date: '',
    signup_end_date: '',
    location: '',
    max_participants: 0,
    min_participants: 0,
    price: 0,
    image: null,
    signup_required: false,
});
const error = ref({});
const props = defineProps({
    modelOpen:{
        type: Boolean,
        default: false,
    }
});
const currentComponent = computed(()=> {
    return `Step${step.value}`;
});

function validateStep() {
    error.value = {};
    switch (step.value) {
        case 1:
            break;
    }
}

</script>