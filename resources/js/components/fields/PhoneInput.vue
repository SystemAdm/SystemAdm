<template>
    <div class="mb-5">
        <label class="block text-gray-700 text-sm font-bold mb-2" :for="id">
            {{ trans(labelKey) }} <span class="text-red-700">*</span>
            <span v-if="infoKey" class="block text-gray-400 text-sm">
                {{ trans(infoKey) }}
            </span>
        </label>
        <input
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700
                   leading-tight focus:outline-none focus:shadow-outline"
            :class="{'border-red-700': error}"
            :id="id"
            type="text"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            required
            :placeholder="trans(placeholderKey)"
            @focus="$emit('clearError')"
        />
        <span v-if="error" class="ml-3 mt-2 block text-red-700">
            {{ error }}
        </span>
    </div>
</template>

<script setup>
import {trans} from "laravel-vue-i18n";

defineProps({
    id: {
        type: String,
        required: true
    },
    modelValue: {
        type: String,
        required: true
    },
    error: {
        type: [String, Boolean],
        default: false
    },
    labelKey: {
        type: String,
        required: true
    },
    infoKey: {
        type: String,
        default: ''
    },
    placeholderKey: {
        type: String,
        required: true
    }
});

defineEmits(['update:modelValue', 'clearError']);
</script>
