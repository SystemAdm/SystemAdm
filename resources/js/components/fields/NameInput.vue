<template>
    <div class="mb-5">
        <label class="block text-gray-700 text-sm font-bold mb-2" :for="id">
            {{ trans('common.' + id) }} <span v-if="required" class="text-red-700">*</span>
        </label>
        <input
            :class="[
                'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline',
                hasError ? 'border-red-700' : ''
            ]"
            :id="id"
            type="text"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :required="required"
            :placeholder="trans('common.' + id)"
            @focus="$emit('sendErrors', { [id]: false })"
        />
        <span v-if="hasError" class="ml-3 mt-2 block text-red-700">{{ hasError }}</span>
    </div>
</template>

<script setup>
import {trans} from "laravel-vue-i18n";

defineProps({
    id: String,
    modelValue: String,
    required: {
        type: Boolean,
        default: false
    },
    hasError: [String, Boolean]
});

defineEmits(['update:modelValue', 'sendErrors']);
</script>
