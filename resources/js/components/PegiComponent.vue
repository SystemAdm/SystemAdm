<template>
    <img 
        :src="pegi" 
        :width="size" 
        :height="size" 
        alt="PEGI rating"
    />
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    age: {
        type: Number,
        required: true
    },
    size: {
        type: Number,
        default: 24
    }
});

// Beregn riktig PEGI-rating basert på alder
const pegi = computed(() => {
    if (props.age >= 18) return '/images/pegi/age-18.jpg';
    if (props.age >= 16) return '/images/pegi/age-16.jpg';
    if (props.age >= 12) return '/images/pegi/age-12.jpg';
    if (props.age >= 7) return '/images/pegi/age-7.jpg';
    return '/images/pegi/age-3.jpg';
});
</script>
