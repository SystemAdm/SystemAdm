<template>
    <div>
    <button
        @click.prevent="emits('goPermanent')"
        type="button"
        class="btn btn-danger"
    >
        <font-awesome-icon :icon="['fas', 'eraser']" class="mr-2"/>
        {{ trans('Permanent') }}
    </button>
    </div>
</template>
<script setup>
import {trans} from "laravel-vue-i18n";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const emits = defineEmits(['goPermanent']);
</script>
