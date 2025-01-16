<template>
    <div>
    <button
        @click.prevent="emits('goBack')"
        type="button"
        class="btn btn-danger"
    >
        <font-awesome-icon :icon="['fas', 'trash-can']" class="mr-2"/>
        {{ trans('Delete') }}
    </button>
    </div>
</template>
<script setup>
import {trans} from "laravel-vue-i18n";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const emits = defineEmits(['goBack']);
</script>
