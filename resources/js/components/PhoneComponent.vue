<template>
    <div class="text-gray-800">

        <PhoneInput
            id="phone"
            required
            placeholder-key="common.enter_phonenumber"
            :labelKey="isGuardian ? 'auth.guardian_phone_label' : 'auth.phone_label'"
            :modelValue="phone"
            @clearError="clearPhoneError"
            @update:modelValue="updatePhone"

        ></PhoneInput>

        <span
            v-if="hasPhoneError"
            class="m-5 block text-red-700"
        >
            {{ hasErrors.phone }}
        </span>

        <span
            v-if="!isGuardian"
            class="block text-sm hover:cursor-pointer my-2 text-blue-700"
            @click="handleEmailClick"
        >
            {{ trans('auth.use_email_instead') }}
        </span>

        <ButtonBar
            :hasNext="true"
            :hasRequired="true"
            :currentStep="currentStep"
            @handleNext="handleNext"
            @handleClose="handleClose"
        />
    </div>
</template>

<script setup>
import ButtonBar from "./ButtonBar.vue";
import {computed, ref} from 'vue';
import axios from "axios";
import {trans} from 'laravel-vue-i18n'
import PhoneInput from "./fields/PhoneInput.vue";

const props = defineProps({
    hasErrors: {
        type: Object,
        required: true
    },
    prev: {
        type: Array,
        default: () => []
    },
    isGuardian: {
        type: Boolean,
        default: false
    },
    currentStep: {
        type: Number,
        required: true
    }
});

const emit = defineEmits(['sendErrors', 'handleSuccess', 'handleClose', 'handleEmail', 'handleBack']);
const phone = ref('');
const updatePhone = (string) => phone.value = string;

const hasPhoneError = computed(() => props.hasErrors.phone);

const handleNext = async () => {
    if (!phone.value) {
        emit('sendErrors', {phone: trans('auth.validation.phone_required')});
        return;
    }

    try {
        const response = await axios.post('/api/users/validate_phone', {
            phone: phone.value
        });

        handleResponse(response.data);
    } catch (error) {
        console.error('API Error:', error);
        emit('sendErrors', {phone: trans('auth.validation.phone_error')});
    }
};

const handleResponse = (data) => {
    if (data === 0) {
        emit('sendErrors', {phone: trans('auth.validation.phone_invalid')});
    } else {
        emit('handleSuccess', {
            data: data === 1 ? 1 : data,
            phone: phone.value,
            isGuardian: props.isGuardian
        });
    }
};

const clearPhoneError = () => emit('sendErrors', {phone: false});
const handleEmailClick = () => emit('handleEmail');
const handleClose = () => {
    if (props.isGuardian) {
        handleBack();
    } else {
        emit('handleClose');
    }
};

const handleBack = () => {
    emit('handleBack');
};
</script>
