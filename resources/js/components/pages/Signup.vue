<!-- Signup.vue -->
<template>
    <div v-if="open" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full px-2">
        <div class="relative m-3 mx-auto shadow-xl rounded-md bg-white">
            <div class="p-6 text-center">
                <component
                    :is="currentComponent"
                    v-bind="componentProps"
                    @handleClose="emit('handleClose')"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import {computed, getCurrentInstance, inject, onMounted, ref} from 'vue';
import {useRouter} from 'vue-router';
import PhoneComponent from "../NewPhoneComponent.vue";
//import PhoneComponent from "../PhoneComponent.vue";
import SelectNameComponent from "../SelectNameComponent.vue";
import PasswordCheckComponent from "../PasswordCheckComponent.vue";
import NameComponent from "../NameComponent.vue";
import SelectCreationComponent from "../SelectCreationComponent.vue";
import QrCode from "../modals/QrCode.vue";
import BirthdayComponent from "../BirthdayComponent.vue";
import SummaryAgreement from "../SummaryAgreement.vue";
import axios from 'axios';
import EmailComponent from "../EmailComponent.vue";
import EmailCheckComponent from "../EmailCheckComponent.vue";
import PasswordComponent from "../PasswordComponent.vue";
import TwilioValidation from "../TwilioValidation.vue";

const app = getCurrentInstance()?.parent;
const router = useRouter();
const fetchAuthenticatedUser = inject('fetchAuthenticatedUser');
const step = ref(1);
const prev = ref([]);
const props = defineProps({
    open: {
        type: Boolean,
        default: false
    }
});
const emit = defineEmits(['handleClose']);
const STEPS = {
    PHONE: 1,          // Telefonnummer input
    EMAIL: 2,          // E-post input
    SELECT_NAME: 3,    // Velg eksisterende navn eller opprett nytt
    PASSWORD_CHECK: 4, // Bekreft passord for eksisterende bruker
    NAME: 5,          // Opprett ny bruker (navn)
    BIRTHDAY: 6,      // Fødselsdato input
    GUARDIAN_PHONE: 7, // Foresatt telefon
    GUARDIAN_EMAIL: 8, // Foresatt e-post
    GUARDIAN_SELECT: 9,// Velg eksisterende foresatt eller opprett ny
    GUARDIAN_NAME: 10, // Opprett ny foresatt
    SELECT_CREATION: 11,   // Velg opprettelsestype
    EMAIL_SETUP: 12,    // E-post oppsett
    SET_PASSWORD: 13,    // Sett passord
    SUMMARY_AGREEMENT: 14,  // Sjekk at dette er riktig nummer
    SHOW_QR: 20,        // Oppdatert stegnummer
    TERMS: 15,           // Terms & Agreement
    TWILIO: 16,          // SMS Validation
};
const handleBack = (stage) => {
    if (stage != null) {
        prev.value = [];
        step.value = stage;
    }
    if (prev.value.length > 0) {
        prev.value.pop();
        step.value = prev.value[prev.value.length - 1];
    }
}

const handleEmail = (email) => {
}

const handlePhone = (phone) => {
}

// Håndterer errors
const handleErrors = (errors) => {
};

// Håndterer suksess fra komponenter
const handleSuccess = async (data) => {
    console.log('handleSuccess called with:', data);

    switch (step.value) {
        case STEPS.EMAIL:
        case STEPS.PHONE:
        case STEPS.TWILIO:
        case STEPS.PASSWORD_CHECK:
        case STEPS.NAME:
        case STEPS.BIRTHDAY:
        case STEPS.SUMMARY_AGREEMENT:
        case STEPS.SELECT_CREATION:
        case STEPS.EMAIL_SETUP:
        case STEPS.SET_PASSWORD:
    }

    console.log('Current step after update:', step.value);
};

// Hjelpefunksjon for å lagre all data
const saveAllData = async () => {
    try {
        const userResponse = await axios.post('/api/users', {});
    } catch (error) {
        throw error;
    }
};

// Håndterer valg av profil (steg 3, 9)
const handleSelectProfile = (user) => {

};

// Håndterer opprettelse av ny profil (steg 3, 9)
const handleCreateNew = () => {

};

// Håndterer registrering fra steg 11
const handleRegister = () => {

};

// Håndterer QR kode visning
const handleShowQR = () => {

};

// Håndterer innlogging
const handleLogin = async () => {

};

const currentComponent = computed(() => {
    if (!step.value || !Object.values(STEPS).includes(step.value)) {
        step.value = STEPS.PHONE;
    }

    switch (step.value) {
        case STEPS.EMAIL:
        case STEPS.GUARDIAN_EMAIL:
            return EmailCheckComponent;
        case STEPS.PHONE:
        case STEPS.GUARDIAN_PHONE:
            return PhoneComponent;
        case STEPS.SELECT_NAME:
        case STEPS.GUARDIAN_SELECT:
            return SelectNameComponent;
        case STEPS.PASSWORD_CHECK:
            return PasswordCheckComponent;
        case STEPS.NAME:
        case STEPS.GUARDIAN_NAME:
            return NameComponent;
        case STEPS.BIRTHDAY:
            return BirthdayComponent;
        case STEPS.SUMMARY_AGREEMENT:
            return SummaryAgreement;
        case STEPS.SELECT_CREATION:
            return SelectCreationComponent;
        case STEPS.SHOW_QR:
            return QrCode;
        case STEPS.EMAIL_SETUP:
            return EmailComponent;
        case STEPS.SET_PASSWORD:
            return PasswordComponent;
        case STEPS.TWILIO:
            return TwilioValidation;
        default:
            return PhoneComponent;  // Return default component instead of null
    }
});

const componentProps = computed(() => {
    const baseProps = {

    };

    const componentSpecificProps = {
        [STEPS.SELECT_NAME]: {},
        [STEPS.GUARDIAN_SELECT]: {},
        [STEPS.PASSWORD_CHECK]: {},
        [STEPS.SUMMARY_AGREEMENT]: {},
        [STEPS.SELECT_CREATION]: {},
        [STEPS.SHOW_QR]: {},
        [STEPS.EMAIL_SETUP]: {},
        [STEPS.SET_PASSWORD]: {}
    };

    return {
        ...baseProps,
        ...(componentSpecificProps[step.value] || {})
    };
});

// Lag en felles aktiveringsmetode
const activateUser = async () => {

};

// Oppdater saveAndLogin og saveGuardianAndChild
const saveAndLogin = async () => {

};

const resetRegistration = () => {

};

onMounted(() => {

})

</script>

