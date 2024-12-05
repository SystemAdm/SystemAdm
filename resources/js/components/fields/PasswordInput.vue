<template>
    <div class="mb-5">
        <label class="block text-gray-700 text-sm font-bold mb-2" :for="id">
            {{ trans(labelKey) }} <span class="text-red-700">*</span>
            <span v-if="infoKey" class="block text-gray-400 text-sm">
                {{ trans(infoKey) }}
            </span>
        </label><div class="relative">
        <input
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            :class="{'border-red-700': error}"
            :id="id"
            :type="showPassword?'text':'password'"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="trans(placeholderKey)"
            @focus="$emit('clearError')"
        ><button
        type="button"
        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-600"
        @click="togglePassword"
        :disabled="isLoading">
        <font-awesome-icon
            :icon="['fas', showPassword ? 'eye-slash' : 'eye']"
            class="hover:text-gray-800"
        />
    </button>
    </div>
        <span v-if="error" class="mt-2 block text-red-700">
            {{ trans(error) }}
        </span>
    </div>
</template>

<script setup>
import {trans} from "laravel-vue-i18n";
import {ref} from "vue";

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
    },
    isLoading: {
        type: Boolean,
        default: false
    }
});
const showPassword = ref(false);
const togglePassword = () => showPassword.value = !showPassword.value;
defineEmits(['update:modelValue', 'clearError']);
</script>
