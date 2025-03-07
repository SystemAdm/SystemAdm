<template>
    <div>
    <button
        @click.prevent="emits('goCancel')"
        type="button"
        class="btn btn-danger"
    >
        <font-awesome-icon :icon="['fas', 'bell']" class="mr-2"/>
        {{ trans('events.Cancel') }}
    </button>
    </div>
</template>
<script setup>
import {trans} from "laravel-vue-i18n";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const emits = defineEmits(['goCancel']);
</script>
