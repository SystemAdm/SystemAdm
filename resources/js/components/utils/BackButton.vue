<template>
    <div>
    <button
        @click.prevent="emits('goBack')"
        type="button"
        class="btn btn-default"
    >
        <font-awesome-icon :icon="['fas', 'arrow-left']" class="mr-2"/>
        {{ trans('Back') }}
    </button>
    </div>
</template>
<script setup>
import {trans} from "laravel-vue-i18n";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const emits = defineEmits(['goBack']);
</script>
