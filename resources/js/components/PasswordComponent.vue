<template>
    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
        Password <span class="text-red-700">*</span>
        <span
            class="block text-gray-400 text-sm">This is YOUR password, we will never ask for it,<br/>neither will anyone else without bad intentions</span>
    </label>
    <!-- INPUT TEXT PHONE -->
    <input
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        id="password"
        type="password"
        placeholder="Password"
        v-model="password"
        required
        :class="{'border-red-700':errors.password}"
        @focus="errors.password = false">
    <span class="ml-3 mt-2 block text-red-700" v-if="errors.password">{{ errors.password }}</span>
    <label class="block text-gray-700 text-sm font-bold mb-2 mt-5" for="password_confirm">
        Password confirmation<span class="text-red-700">*</span>
        <span
            class="block text-gray-400 text-sm">Please confirm your password by retyping it</span>
    </label>
    <!-- INPUT TEXT PHONE -->
    <input
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        id="password_confirmed"
        type="password"
        placeholder="Password confirmation"
        v-model="password_confirmed"
        required
        :class="{'border-red-700':errors.password_confirmed}"
        @focus="errors.password_confirmed = false">
    <span class="ml-3 mt-2 block text-red-700" v-if="errors.password_confirmed">{{
            errors.password_confirm
        }}</span>
    <ButtonBar :current-step="currentStep" :next="true" @close="emit('close')" @go="check" @back="emit('back')"></ButtonBar>
</template>
<script setup>
import ButtonBar from "./ButtonBar.vue";
import { ref } from 'vue';

const props = defineProps({
    errors: {
        type: Object,
        required: true
    },
    currentStep: {
        type: Number,
        required: true
    }
});

const emit = defineEmits(['sendErrors', 'close', 'back', 'next']);

const password = ref(null);
const password_confirmed = ref(null);

const check = () => {
    if (!password.value || !password_confirmed.value) {
        emit('sendErrors', {
            password: !password.value ? 'Password is required' : false,
            password_confirmed: !password_confirmed.value ? 'Password confirmation is required' : false
        });
        return;
    }

    if (password.value !== password_confirmed.value) {
        emit('sendErrors', {
            password_confirmed: 'Passwords do not match'
        });
        return;
    }

    emit('next', {
        password: password.value,
        password_confirmed: password_confirmed.value
    });
};
</script>
