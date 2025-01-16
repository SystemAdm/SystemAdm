<template>
    <form class="mt-6">
        <div class="mb-4">
            <h4 class="mb-3" v-if="props.isGuardian">{{ trans('auth.guardian_input') }}:
                <span class="text-gray-500 text-xs text-justify w-full">/* <span
                    v-html="trans('auth.guardian_types')"></span> */</span>
            </h4>
            <label for="input" class="block mb-2 text-sm font-medium">
                {{ trans('auth.input') }}
            </label>
            <div class="relative">
                <input
                    type="text"
                    id="input"
                    v-model="inputValue"
                    :placeholder="trans('auth.input_placeholder')"
                    class="w-full px-4 py-2 focus:ring-1 focus:outline-1 rounded-lg border
                    bg-gray-100
                    border-gray-700
                    dark:bg-gray-600
                    dark:border-gray-700
                    dark:text-gray-200

                    focus:ring-indigo-500

                    placeholder:text-gray-600
                    dark:placeholder:text-gray-400"
                    :class="{ 'border-red-500': errorField.value === 'input' }"
                    required
                    @input="inputValue.length > 5 ? isDisabled = false : isDisabled = true"
                />
                <font-awesome-icon class="absolute end-3 top-3" :icon="['fas', 'phone']"/>
            </div>
            <div class="mt-2" v-if="props.isGuardian">
                <select v-model="relation"
                        :class="{'border-red-500 dark:border-red-500' : errorField.value === 'relation'}"
                        class="block w-full px-4 py-2 text-gray-700 bg-gray-100 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="0" disabled class="disabled:text-gray-400">{{ trans('auth.relation') }}:</option>
                    <option value="1">{{ trans('auth.mother') }}</option>
                    <option value="2">{{ trans('auth.father') }}</option>
                    <option value="01">{{ trans('auth.step_mother') }}</option>
                    <option value="02">{{ trans('auth.step_father') }}</option>
                    <optgroup :label="trans('auth.grandparents')">
                        <option value="11">{{ trans('auth.mothers_mother') }}</option>
                        <option value="12">{{ trans('auth.mothers_father') }}</option>
                        <option value="21">{{ trans('auth.fathers_mother') }}</option>
                        <option value="22">{{ trans('auth.fathers_father') }}</option>
                    </optgroup>
                    <optgroup :label="trans('auth.other')">
                        <option value="31">{{ trans('auth.aunt') }}</option>
                        <option value="32">{{ trans('auth.uncle') }}</option>
                        <option value="33">{{ trans('auth.guardian') }}</option>
                        <option value="34">{{ trans('auth.leader') }}</option>
                        <option value="35">{{ trans('auth.other') }}</option>
                    </optgroup>
                </select>
            </div>
            <ErrorMessage :errorMessage="errorMessage"></ErrorMessage>
        </div>

        <div class="flex justify-end mt-6" :class="{'justify-between' : props.isGuardian}">
            <BackButton v-if="isGuardian" @goBack="emits('goBack')" />
            <NextButton @goNext="goNext" :isLoading="isLoading" :isDisabled="isDisabled"/>
        </div>
    </form>
</template>

<script setup>
import {defineEmits, defineProps, ref} from 'vue';
import {trans} from 'laravel-vue-i18n';
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import NextButton from "../NextButton.vue";
import ErrorMessage from "./ErrorMessage.vue";
import BackButton from "../utils/BackButton.vue";
import axios from "axios";

const props = defineProps({
    isGuardian: {
        type: Boolean,
        default: false,
        required: false,
    }
});

const emits = defineEmits(['goNext', 'goBack']);
const inputValue = ref('');
const errorMessage = ref('');
const errorField = ref('');
const input = ref('');
const inputType = ref('');
const isLoading = ref(false);
const relation = ref(0);
const isDisabled = ref(true);

// Emit event to parent with the input value
async function goNext() {
    isLoading.value = true;
    if (props.isGuardian && relation.value === 0) {
        errorMessage.value = trans('auth.invalid_relation');
        errorField.value = 'relation';
        isLoading.value = false;
        return;
    }
    if (inputValue.value === '') {
        errorMessage.value = trans('auth.invalid_input');
        errorField.value = 'input';
        isLoading.value = false;
        return;
    }

    try {
        const response = await axios.post('/api/users/validate_input', {
            input: inputValue.value
        });
        const {valid, object, data, innData} = response.data;

        if (valid) {
            if (data && typeof data === 'object' && Object.keys(data).length > 0) {
                emits('goNext', {
                    step: 'next',
                    valid: valid,
                    innData: innData,
                    inputType: object,
                    users: data
                });
            } else {
                emits('goNext', {
                    step: 'new',
                    valid: valid,
                    innData: innData,
                    inputType: object,
                    users: null,
                });
            }
        } else {
            if (object === 'phone') {
                errorMessage.value = trans('auth.invalid_phone');
            } else if (object === 'email') {
                errorMessage.value = trans('auth.invalid_email');
            } else {
                errorMessage.value = trans('auth.invalid_input');
            }
        }
    } catch (error) {
        errorMessage.value = error.response?.data?.message || trans('auth.error_message');
        console.error(error);
    } finally {
        isLoading.value = false;
    }
}
</script>
