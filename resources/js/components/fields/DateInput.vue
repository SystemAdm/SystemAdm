<template>
    <div class="mb-5">
        <label class="block text-gray-700 text-sm font-bold mb-2" :for="id">
            {{ $t(labelKey) }} <span v-if="required" class="text-red-700">*</span>
            <span v-if="infoKey" class="block text-gray-400 text-sm">
                {{ $t(infoKey) }}
            </span>
        </label>
        
        <input
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            :class="{'border-red-700': error}"
            :id="id"
            type="date"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :required="required"
            @focus="$emit('clearError')"
            @change="$emit('change', $event.target.value)"
        >
        
        <span v-if="error" class="ml-3 mt-2 block text-red-700">
            {{ error }}
        </span>
    </div>
</template>

<script setup>
defineProps({
    id: {
        type: String,
        required: true
    },
    modelValue: {
        type: String,
        default: null
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
    required: {
        type: Boolean,
        default: false
    }
});

defineEmits(['update:modelValue', 'clearError', 'change']);
</script>

<style scoped>
input[type="date"] {
    /* Fjerner default styling på date input i webkit browsers */
    appearance: none;
    -webkit-appearance: none;
}

input[type="date"]::-webkit-calendar-picker-indicator {
    /* Styling av kalender-ikonet */
    cursor: pointer;
    opacity: 0.6;
    padding: 0.2rem;
}

input[type="date"]::-webkit-calendar-picker-indicator:hover {
    opacity: 1;
}
</style>