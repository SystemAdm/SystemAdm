<template>
    <div class="p-4 dark:bg-gray-900 dark:text-white">
        <!-- Overskrifter og felter -->
        <h4 class="mb-3" v-if="props.isGuardian">{{ trans('auth.guardian_name') }}:</h4>
        <!-- Fornavn -->
        <div class="mb-4">
            <label for="given_name" class="block text-sm font-medium mb-1">
                {{ trans('auth.given_name') }}<span class="text-red-500 pl-2">*</span>
            </label>
            <input
                id="given_name"
                v-model="name.given_name"
                type="text"
                required
                class="w-full p-2 border border-gray-300 rounded dark:border-gray-700 dark:bg-gray-800"
                :class="{' dark:border-red-500 border-red-500' : errorField === 'given_name'}"
            />
        </div>

        <!-- Mellomnavn -->
        <div class="mb-4">
            <label for="additional_name" class="block text-sm font-medium mb-1">
                {{ trans('auth.additional_name') }}
            </label>
            <input
                id="additional_name"
                v-model="name.additional_name"
                type="text"
                class="w-full p-2 border border-gray-300 rounded dark:border-gray-700 dark:bg-gray-800"
            />
        </div>

        <!-- Etternavn -->
        <div class="mb-4">
            <label for="family_name" class="block text-sm font-medium mb-1">
                {{ trans('auth.family_name') }}<span class="text-red-500 pl-2">*</span>
            </label>
            <input
                id="family_name"
                v-model="name.family_name"
                required
                type="text"
                class="w-full p-2 border border-gray-300 rounded dark:border-gray-700 dark:bg-gray-800 "
                :class="{' dark:border-red-500 border-red-500' : errorField === 'family_name'}"
            />
        </div>
        <!-- Feilmelding -->
        <p v-if="errorMessage" class="mt-4 text-center text-red-500">
            {{ errorMessage }}
        </p>

        <!-- Knappene -->
        <div class="flex justify-between mt-6">
            <BackButton @goBack="emits('goBack')"/>
            <NextButton @goNext="goNext"/>
        </div>
    </div>
</template>

<script setup>
import {trans} from "laravel-vue-i18n";
import {ref} from "vue";
import BackButton from "../utils/BackButton.vue";
import NextButton from "../utils/NextButton.vue";

const errorMessage = ref('');
const name = ref({
    family_name: '',
    given_name: '',
    additional_name: null
});
const errorField = ref('');
const emits = defineEmits(['goBack', 'goNext']);
const props = defineProps({
    guardian: {
        type: Boolean,
        default: false
    },
});

function goNext() {
    errorMessage.value = '';
    errorField.value = '';
    console.log(name.value);
    if (name.value.family_name === '') {
        errorMessage.value = trans('auth.family_name_error');
        errorField.value = 'family_name';
        return;
    }
    if (name.value.given_name === '') {
        errorMessage.value = trans('auth.given_name_error');
        errorField.value = 'given_name';
        return;
    }
    emits('goNext', name);
}
</script>
