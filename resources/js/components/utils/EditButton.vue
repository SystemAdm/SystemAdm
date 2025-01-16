<template>
    <div>
        <button
            @click.prevent="emits('goBack')"
            type="button"
            class="btn btn-warning"
        >
            <font-awesome-icon :icon="['fas', 'pen-to-square']" class="mr-2"/>
            {{ trans('Edit') }}
        </button>
    </div>
</template>
<script setup>
import {trans} from "laravel-vue-i18n";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const emits = defineEmits(['goBack']);
</script>
