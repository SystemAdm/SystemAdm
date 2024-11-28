<template>
    <a href="https://www.esrb.org/ratings-guide/" target="_blank">
        <img 
            :src="esrbImage" 
            :alt="esrbRating" 
            :width="size" 
            :height="size"
        >
    </a>
</template>

<script setup>
import { ref, watch, computed } from 'vue';

const props = defineProps({
    age: {
        type: Number,
        required: true,
    },
    size: {
        type: Number,
        default: 24
    }
});

const ESRB_RATINGS = {
    AO: { minAge: 18, file: 'AO.svg' },
    M: { minAge: 17, file: 'M.svg' },
    T: { minAge: 13, file: 'T.svg' },
    E10: { minAge: 10, file: 'E10plus.svg' },
    E: { minAge: 0, file: 'E.svg' }
};

const currentRating = ref('E');

const esrbImage = computed(() => `/images/esrb/${ESRB_RATINGS[currentRating.value].file}`);
const esrbRating = computed(() => currentRating.value);

watch(() => props.age, (val) => {
    if (val >= ESRB_RATINGS.AO.minAge) {
        currentRating.value = 'AO';
    } else if (val >= ESRB_RATINGS.M.minAge) {
        currentRating.value = 'M';
    } else if (val >= ESRB_RATINGS.T.minAge) {
        currentRating.value = 'T';
    } else if (val >= ESRB_RATINGS.E10.minAge) {
        currentRating.value = 'E10';
    } else {
        currentRating.value = 'E';
    }
}, { immediate: true });
</script>
