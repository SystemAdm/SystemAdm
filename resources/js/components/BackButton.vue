<template>
    <button
        @click.prevent="emits('goBack')"
        type="button"
        class="inline-flex items-center px-4 py-2 text-sm font-medium focus:ring-1 border
        bg-gray-300
        border-gray-500 rounded-lg
        hover:bg-gray-200

        dark:bg-gray-800
        dark:border-gray-700
        dark:hover:bg-gray-700"
    >
        <font-awesome-icon :icon="['fas', 'arrow-left']" class="mr-2"/>
        {{ trans('auth.back') }}
    </button>
</template>
<script setup>
import {trans} from "laravel-vue-i18n";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const emits = defineEmits(['goBack']);
</script>
