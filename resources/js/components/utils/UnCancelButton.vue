<template>
    <div>
    <button
        @click.prevent="emits('goUncancel')"
        type="button"
        class="btn btn-success"
    >
        <font-awesome-icon :icon="['fas', 'bell-slash']" class="mr-2"/>
        {{ trans('Uncancel') }}
    </button>
    </div>
</template>
<script setup>
import {trans} from "laravel-vue-i18n";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const emits = defineEmits(['goUncancel']);
</script>
