<template>
    <div>
    <button
        @click.prevent="emits('goRecover')"
        type="button"
        class="btn btn-success"
    >
        <font-awesome-icon :icon="['fas', 'trash-can-arrow-up']" class="mr-2"/>
        {{ trans('Recover') }}
    </button>
    </div>
</template>
<script setup>
import {trans} from "laravel-vue-i18n";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const emits = defineEmits(['goRecover']);
</script>
