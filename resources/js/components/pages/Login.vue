<template>
    <div class="flex justify-center items-center h-full">
        <div
            class="max-w-md p-8 w-full shadow-lg rounded-lg dark:shadow-md text-black dark:text-white bg-white dark:bg-gray-900">
            <h2 class="text-2xl font-bold text-center">
                {{ trans('auth.login_title') }}
            </h2>

            <!-- Input Form -->
            <CheckInputForm
                v-if="currentStep === steps.INPUT_GUARDIAN_DATA || currentStep === steps.INPUT_DATA"
                @goNext="handleCheckInputForm"
                @goBack="goBack"
                :isGuardian="isGuardian"
            />

            <!-- User List -->
            <UserList
                v-if="currentStep === steps.SELECT_USER"
                :users="users"
                :innData="innData"
                :isGuardian="isGuardian"
                @goBack="goBack"
                @selectUser="handleSelectUser"
            />

            <!-- Validate Password -->
            <PasswordValidation
                v-if="currentStep === steps.VALIDATE_PASSWORD"
                :selectedUser="selectedUser"
                @goBack="goBack"
                @goNext="handlePasswordValidation"
                @reset="handleResetPassword"
            />
            <VerifyInput
                v-if="currentStep === steps.VERIFY_INPUT"
                :innData="innData"
                @goBack="goBack"
                @goNext="handleVerifyInput"
            />
            <Choices v-if="currentStep === steps.CHOICES" :selectedUser="selectedUser" :loggedIn="loggedIn"
                     @goBack="goBack" @goLogin="goLogin"/>
            <InputPassword
                v-if="currentStep === steps.INPUT_PASSWORD"
                :selectedUser="selectedUser"
                :isReset="isReset"
                @goBack="goBack"
                @goNext="handleInputPassword"
            />

            <InputName
                v-if="currentStep === steps.INPUT_GUARDIAN_NAME || currentStep === steps.INPUT_NAME"
                :isGuardian="isGuardian"
                @goBack="goBack"
                @goNext="handleSaveName"
            />

            <InputBirth
                v-if="currentStep === steps.INPUT_BIRTH"
                @goBack="goBack"
                @goNext="handleSaveBirth"
            />

            <ConfirmRules
                v-if="currentStep === steps.CONFIRM_RULES"
                @goBack="goBack"
                @goNext="handleConfirmRules"
            />
            <Summary
                v-if="currentStep === steps.SUMMARY"
                :createUser="createUser"
                :createGuardian="createGuardian"
                :selectedUser="selectedUser"
                :selectedGuardian="selectedGuardian"
                @goBack="goBack"
                @goNext="handleSummary"
            />

            <!-- Error Message -->
            <ErrorMessage :errorMessage="errorMessage"/>
        </div>
    </div>
</template>

<script setup>
import {ref} from 'vue';
import {useRouter} from "vue-router";
import axios from 'axios';
import {trans} from 'laravel-vue-i18n';
import {loginUser, createUser} from "../../services/api.js";

import CheckInputForm from '../form/CheckInputForm.vue';
import UserList from '../form/UserList.vue';
import ErrorMessage from '../form/ErrorMessage.vue';
import PasswordValidation from "../form/PasswordValidation.vue";
import VerifyInput from "../form/VerifyInput.vue";
import InputPassword from "../form/InputPassword.vue";
import InputName from "../form/InputName.vue";
import InputBirth from "../form/InputBirth.vue";
import ConfirmRules from "../form/ConfirmRules.vue";
import Summary from "../form/Summary.vue";
import Choices from "../form/Choices.vue";
import {useUserStore} from "@/stores/userStore.js";

const router = useRouter();
const userStore = useUserStore();
// Reactive data
const isReset = ref(false);
const isGuardian = ref(false);
const back = ref([]);
const currentStep = ref(1);
const users = ref([]);
const guardians = ref({});
const selectedUser = ref(null);
const errorMessage = ref('');
const isLoading = ref(false);
const innData = ref({ value: '', type: '' });
const selectedGuardian = ref(null);
const loggedIn = ref(false);

const steps = {
    INPUT_DATA: 1,
    SELECT_USER: 2,
    INPUT_NAME: 3,
    INPUT_BIRTH: 4,
    INPUT_GUARDIAN_DATA: 5,
    SELECT_GUARDIAN: 6,
    INPUT_GUARDIAN_NAME: 7,
    VALIDATE_PASSWORD: 8,
    CHOICES: 9,
    VERIFY_INPUT: 10,
    INPUT_PASSWORD: 11,
    CONFIRM_RULES: 12,
    SUMMARY: 13,
}

const createUserState = ref({
    given_name: '',
    family_name: '',
    additional_name: '',
    input: '',
    inputType: '',
    password: '',
    password_confirmation: '',
    birthdate: '',
    age: 4,
    verified: false,
});

const createGuardianState = ref({
    given_name: '',
    additional_name: '',
    family_name: '',
    input: '',
    inputType: '',
});

// Funskjoner
function goBack() {
    if (back.value.length > 0) {
        currentStep.value = back.value.pop();
    }
}

function updateStep(nextStep, shouldSaveCurrent = true) {
    if (shouldSaveCurrent) {
        back.value.push(currentStep.value);
    }
    currentStep.value = nextStep;
}

async function handleCheckInputForm(values) {
    innData.value.value = values.innData;
    innData.value.type = values.inputType;

    if (isGuardian.value) {
        if (values.step === 'next') {
            guardians.value = values.users;
            updateStep(steps.SELECT_GUARDIAN);
        } else if (values.step === 'new') {
            createGuardianState.value.input = values.innData;
            createGuardianState.value.inputType = values.inputType;
            updateStep(steps.INPUT_GUARDIAN_NAME);
        }
    } else {
        if (values.step === 'next') {
            users.value = values.users;
            updateStep(steps.SELECT_USER);
        } else if (values.step === 'new') {
            createUserState.value.inputType = values.inputType;
            createUserState.value.input = values.innData;
            updateStep(values.step);
        }
    }
}

function handleVerifyInput() {
    if (isReset.value) {
        updateStep(steps.INPUT_PASSWORD);
    } else {
        createUserState.value.verified = true;
        updateStep(steps.INPUT_NAME);
    }
}

async function handleSummary() {
    if (!createUserState.value.family_name || !createUserState.value.given_name) {
        try {
            isLoading.value = true;
            const response = await createUser(createUserState.value, createGuardianState.value);
            console.log("Bruker opprettet:", response.data);
            updateStep(steps.SUMMARY, false);
        } catch (error) {
            handleError(error, trans('auth.error_message'));
        } finally {
            isLoading.value = false;
        }
    }
}

async function handleSelectUser(user) {
    selectedUser.value = user;

    if (selectedUser.value.active) {
        updateStep(steps.VALIDATE_PASSWORD);
    } else {
        updateStep(steps.CHOICES);
    }
}

function handleResetPassword() {
    isReset.value = true;
    updateStep(steps.VERIFY_INPUT);
}

function handleError(error, defaultMessage) {
    errorMessage.value = error.response?.data?.message || defaultMessage;
    console.error(error);
}

function goLogin() {
    userStore.clearUser();
    loginUser(selectedUser.value.id)
        .then(() => {
            loggedIn.value = true;
            router.push({ name: 'Dashboard' });
        })
        .catch((error) => {
            handleError(error, trans('auth.login_failed_message'));
        });
}


/*
const createUser = ref({
    given_name: '',
    family_name: '',
    additional_name: '',
    input: '',
    inputType: '',
    password: '',
    password_confirmation: '',
    birthdate: '',
    age: 4,
    verified: false,
});
const createGuardian = ref({
    given_name: '',
    additional_name: '',
    family_name: '',
    input: '',
    inputType: '',
});
const emits = defineEmits(['update']);

function handleConfirmRules() {
    gotoStep(steps.SUMMARY);
}

function handleSaveBirth(values) {
    createUser.value.birthdate = values.birthDate;
    createUser.value.age = values.age;
    createUser.value.postalcode = values.postcode;

    if (values.age >= 18) {
        gotoStep(steps.CHOICES);
    } else if (values.age < 18) {
        isGuardian.value = true;
        gotoStep(steps.INPUT_GUARDIAN_DATA);
    }
}

async function handleSummary() {
    if (createUser.value.family_name === '' || createUser.value.given_name === '') {
        try {
            const response = await axios.post('/api/users/', {
                user: createUser.value,
                guardian: createGuardian
            }).then(response => {

            })
        } catch (error) {
            errorMessage.value = error.response?.data?.message || trans('auth.error_message');
            console.error(error);
        } finally {
            isLoading.value = false;
        }
    }
}

function handleResetPassword() {
    isReset.value = true;
    gotoStep(steps.VERIFY_INPUT);
}

function gotoStep(step) {
    back.value.push(currentStep.value);
    currentStep.value = step;
}

function goBack() {
    if (back.value.length > 0) {
        currentStep.value = back.value.pop();
    }
}

async function handleInputPassword(n) {
    console.log(n);
    if (n !== undefined) {
        createUser.value.password = n.pd;
    }
    gotoStep(steps.CHOICES);
}

function handleVerifyInput() {
    if (isReset.value) {
        gotoStep(steps.INPUT_PASSWORD);
    } else {
        createUser.value.verified = true;
        gotoStep(steps.INPUT_NAME);
    }
}
*/
function handlePasswordValidation() {
    updateStep(steps.CHOICES);
}
/*
function gotoStep(step) {
    back.value.push(currentStep.value);
    currentStep.value = step;
}

function handleSelectUser(user) {
    console.log(user);
    selectedUser.value = user;
    if (selectedUser.value.active) {
        gotoStep(steps.VALIDATE_PASSWORD);
    } else {
        gotoStep(steps.CHOICES);
    }
}

function handleSelectGuardian(user) {
    console.log(user);
    selectedGuardian.value = user;
    if (selectedGuardian.value.active) {
        gotoStep(steps.INPUT_GUARDIAN_NAME);
    } else {
        gotoStep(steps.CHOICES);
    }
}

async function handleCheckInputForm(values) {
    innData.value.value = values.innData;
    innData.value.type = values.inputType;
    if (isGuardian.value) {
        if (values.step === 'next') {
            guardians.value = values.users;
            gotoStep(steps.SELECT_GUARDIAN);
        } else if (values.step === 'new') {
            createGuardian.value.input = values.innData;
            createGuardian.value.inputType = values.inputType;
            gotoStep(steps.INPUT_GUARDIAN_NAME);
        }
    } else {
        if (values.step === 'next') {
            users.value = values.users;
            gotoStep(steps.SELECT_USER);
        } else if (values.step === 'new') {
            createUser.value.inputType = values.inputType;
            createUser.value.input = values.innData;
            gotoStep(values.step);
        }
    }
}

function handleSaveName(names) {
    if (isGuardian.value) {

    } else {
        createUser.value.family_name = names.value.family_name;
        createUser.value.given_name = names.value.given_name;
        createUser.value.additional_name = names.value.additional_name;
        gotoStep(steps.INPUT_BIRTH);
    }
}

function goLogin() {
    console.log(selectedUser.value);
    axios.post('/api/login', {user_id: selectedUser.value.id}).then(response => {
        emits('update');
        router.push({name: 'Dashboard'});
    });
}*/
</script>
