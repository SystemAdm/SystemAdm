<template>
    <button
        @click.prevent="emits('goNext')"
        type="submit"
        class="inline-flex items-center px-4 py-2 text-sm font-medium focus:ring-1 focus:outline-1 border rounded-lg
        bg-blue-500
        border-blue-900
        text-white

        focus:ring-blue-500

        dark:bg-blue-600
        dark:border-blue-500
        dark:focus:ring-blue-700
        disabled:text-opacity-50
"
        :class="{'hover:bg-blue-600 dark:hover:bg-blue-700 transition':!isDisabled}"
        :disabled="isDisabled"
    >
        <span v-if="isLoading">
            <font-awesome-icon :icon="['fas', 'spinner']" class="animate-spin mr-2"/>
            {{ trans('auth.loading') }}
        </span>
        <span v-else>
            {{ trans('auth.next') }}
            <font-awesome-icon :icon="['fas', 'arrow-right']" class="ml-2"/>
        </span>
    </button>
</template>
<script setup>
import {trans} from "laravel-vue-i18n";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {onMounted} from "vue";

const emits = defineEmits(['goNext']);
const props = defineProps({
    isLoading: {
        type: Boolean,
        default: false,
        required: false,
    },
    isDisabled: {
        type: Boolean,
        default: false,
        required: false,
    }
});
</script>
