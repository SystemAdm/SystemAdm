<template>
    <div class="mt-4 text-sm text-red-500 dark:text-red-400" v-if="errorMessage">
        {{ errorMessage }}
    </div>
</template>

<script setup>
import {defineProps} from 'vue';

const props = defineProps({
    errorMessage: {
        type: String,
        default: ''
    }
});
</script>
