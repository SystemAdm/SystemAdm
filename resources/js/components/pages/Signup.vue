<!-- Signup.vue -->
<template>
    <div class="fixed z-50 inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full px-4">
        <div class="relative top-20 mx-auto shadow-xl rounded-md bg-white max-w-md">
            <div class="p-6 text-center">
                <h2 class="text-2xl font-bold pt-4 pb-6 text-gray-800">
                    {{ $t(pageTitle) }}
                </h2>

                <div v-if="isDebug" class="text-xs text-gray-500 mb-4">
                    Current step: {{ step }}
                </div>

                <component
                    v-if="currentComponent"
                    :is="currentComponent"
                    v-bind="componentProps"
                    @handleEmail="handleEmail"
                    @handlePhone="handlePhone"
                    @sendErrors="handleErrors"
                    @handleSuccess="handleSuccess"
                    @handleClose="$emit('close')"
                    @handleBack="handleBack"
                    @selectProfile="handleSelectProfile"
                    @createNew="handleCreateNew"
                    @showQR="handleShowQR"
                    @handleLogin="handleLogin"
                    @handleRegister="handleRegister"
                    @handleReset="resetRegistration"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import {computed, getCurrentInstance, inject, ref} from 'vue';
import {useRouter} from 'vue-router';
import PhoneComponent from "../PhoneComponent.vue";
import SelectNameComponent from "../SelectNameComponent.vue";
import PasswordCheckComponent from "../PasswordCheckComponent.vue";
import NameComponent from "../NameComponent.vue";
import SelectCreationComponent from "../SelectCreationComponent.vue";
import QrCode from "../modals/QrCode.vue";
import BirthdayComponent from "../BirthdayComponent.vue";
import SummaryAgreement from "../SummaryAgreement.vue";
import axios from 'axios';
import {calculateAge} from '../utils/age.js';
import EmailComponent from "../EmailComponent.vue";
import EmailCheckComponent from "../EmailCheckComponent.vue";
import PasswordComponent from "../PasswordComponent.vue";
import {trans} from 'laravel-vue-i18n';
import {notify} from '../utils/notify';

const app = getCurrentInstance()?.parent;
const router = useRouter();
const fetchAuthenticatedUser = inject('fetchAuthenticatedUser');
const step = ref(1);
const prev = ref([]);
const registration = ref({
    errors: {},
    data: null,
    phone: null,
    email: null,
    selectedProfile: null,
    newUser: {
        given_name: null,
        additional_name: null,
        family_name: null,
        phone: null,
        email: null
    },
    guardian: {
        given_name: null,
        additional_name: null,
        family_name: null,
        phone: null,
        email: null
    },
    birthday: null,
    isGuardian: false,
    termsAccepted: false,
    isRegisteringAsGuardian: false,
});

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
    TERMS: 15           // Terms & Agreement
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
// Debug info
const isDebug = computed(() => import.meta.env.VITE_APP_DEBUG === 'true');
// Computed property for tittel
const pageTitle = computed(() => {
    return registration.value.isGuardian
        ? 'auth.guardian_title'
        : 'auth.signup_title';
});
const handleEmail = (email) => {
    if (registration.value.isGuardian) {
        step.value = STEPS.GUARDIAN_EMAIL;
    } else {
        step.value = STEPS.EMAIL;
    }
}
const handlePhone = (phone) => {
    if (registration.value.isGuardian) {
        step.value = STEPS.GUARDIAN_PHONE;
    } else {
        step.value = STEPS.PHONE;
    }
}
// Håndterer errors
const handleErrors = (errors) => {
    registration.value.errors = errors || {};
};

// Håndterer suksess fra komponenter
const handleSuccess = async (data) => {
    console.log('handleSuccess called with:', data);

    switch (step.value) {
        case STEPS.EMAIL:
        case STEPS.PHONE:
            registration.value.data = data.data;
            registration.value.phone = data.phone;  // Lagre telefonnummer
            registration.value.email = data.email;
            registration.value.selectedProfile = null;  // Reset selectedProfile
            prev.value = [step.value];

            // Case 3 & 4: Eksisterende brukere funnet
            if (Object.keys(data.data).length > 0) {
                step.value = STEPS.SELECT_NAME;
            } else {
                // Case 1, 2 & 7: Ny gjest
                step.value = STEPS.NAME;
            }
            break;

        case STEPS.PASSWORD_CHECK:
            console.log('Password check success');

            prev.value.push(step.value);
            step.value = STEPS.SELECT_CREATION;

            break;

        case STEPS.NAME:
            registration.value.newUser = data;
            step.value = STEPS.BIRTHDAY;
            break;

        case STEPS.BIRTHDAY:
            registration.value.birthday = data.birthday;
            const age = calculateAge(data.birthday);

            if (data.addGuardian || age < 18) {
                // Hvis brukeren klikket "Legg til foresatt" eller er under 18
                registration.value.isGuardian = true;
                prev.value.push(step.value);
                step.value = STEPS.GUARDIAN_PHONE;
            } else {
                prev.value.push(step.value);
                step.value = STEPS.SUMMARY_AGREEMENT;
            }
            break;
        case STEPS.SUMMARY_AGREEMENT:
            if (data.agreed) {
                if (registration.value.newUser) {
                    await saveAllData();  // Lagrer ny bruker først
                    step.value = STEPS.SELECT_CREATION;
                } else if (registration.value.isRegisteringAsGuardian) {
                    await saveAndLogin();  // For eksisterende bruker som blir foresatt
                }
            }
            break;

        case STEPS.SELECT_CREATION:
            switch (data.action) {
                case 'register_as_guardian':
                    if (calculateAge(registration.value.birthday) >= 18) {
                        registration.value.isRegisteringAsGuardian = true;
                        step.value = STEPS.EMAIL_SETUP;
                    }
                    break;
                case 'show_qr':
                    step.value = STEPS.SHOW_QR;
                    break;
            }
            break;

        case STEPS.EMAIL_SETUP:
            registration.value.email = data.email;
            step.value = STEPS.SET_PASSWORD;
            break;

        case STEPS.SET_PASSWORD:
            registration.value.password = data.password;
            step.value = STEPS.SUMMARY_AGREEMENT;
            break;
    }

    // Legg til en sjekk for å verifisere at step.value er satt
    console.log('Current step after update:', step.value);
};

// Hjelpefunksjon for å lagre all data
const saveAllData = async () => {
    try {
        const userResponse = await axios.post('/api/users', {
            ...registration.value.newUser,
            birthday: registration.value.birthday,
            phone: registration.value.phone
        });
        registration.value.savedUser = userResponse.data;
        registration.value.selectedProfile = userResponse.data;  // Legg til dette
    } catch (error) {
        console.error('Feil ved lagring:', error);
        registration.value.errors = error.response?.data?.errors;
        throw error;
    }
};

// Håndterer valg av profil (steg 3, 9)
const handleSelectProfile = (user) => {
    console.log('handleProfileSelect called with user:', user);

    registration.value.selectedProfile = user; // Sets selected USER profile

    prev.value.push(step.value);

    if (registration.value.isGuardian) {
        console.log('Guardian mode, going to SELECT_CREATION');
        step.value = STEPS.SELECT_CREATION;
    } else {
        console.log('User mode, active:', user.active);
        step.value = user.active ? STEPS.PASSWORD_CHECK : STEPS.SELECT_CREATION;
    }

    console.log('New step value:', step.value);
};

// Håndterer opprettelse av ny profil (steg 3, 9)
const handleCreateNew = () => {
    prev.value.push(step.value);
    step.value = STEPS.NAME;
    registration.value.selectedProfile = null;
};

// Håndterer registrering fra steg 11
const handleRegister = () => {
    const user = registration.value.selectedProfile || registration.value.newUser;

    prev.value.push(step.value);
    if (user?.email || registration.value.email) {
        step.value = STEPS.SET_PASSWORD;
    } else {
        step.value = STEPS.EMAIL_SETUP;
    }
};

// Håndterer QR kode visning
const handleShowQR = () => {
    console.log('Showing QR code for user:', registration.value.selectedProfile || registration.value.newUser);
    prev.value.push(step.value);
    step.value = STEPS.SHOW_QR;
};

// Håndterer innlogging
const handleLogin = async () => {
    console.log('Attempting login, password verified:', registration.value.passwordVerified);

    if (!registration.value.passwordVerified) {
        console.error(trans('auth.error.password_not_verified'));
        return;
    }

    if (!registration.value.selectedProfile?.id) {
        console.error(trans('auth.error.no_profile_selected'));
        return;
    }

    try {
        const response = await axios.post('/api/users/login', {
            user_id: registration.value.selectedProfile.id
        });

        // Sett token først
        if (response.data.token) {
            localStorage.setItem('token', response.data.token);
            axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        }

        // Så hent brukerdata
        if (fetchAuthenticatedUser) {
            await fetchAuthenticatedUser();

            notify({
                group: "success",
                title: "Success",
                text: trans('auth.login_success')
            });

            console.log('Login successful, navigating to dashboard');
            router.push('/dashboard');
        } else {
            console.error('fetchAuthenticatedUser not provided');
        }
    } catch (error) {
        console.error('Login error:', error);

        registration.value.errors = error.response?.data?.error
            ? {general: error.response.data.error}
            : {general: 'An unknown error occurred'};

        notify({
            group: "error",
            title: "Error",
            text: trans('auth.login_failed')
        });
    }
};

const currentComponent = computed(() => {
    console.log('Computing component for step:', step.value);

    // Sikre at step har en gyldig verdi
    if (!step.value || !Object.values(STEPS).includes(step.value)) {
        console.warn('Invalid step value:', step.value);
        step.value = STEPS.PHONE;  // Sett til default hvis ugyldig
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
        default:
            console.log('No component found for step:', step.value);
            return PhoneComponent;  // Return default component instead of null
    }
});

const componentProps = computed(() => {
    const baseProps = {
        hasErrors: registration.value.errors,
        prev: prev.value,
        STEPS,
        isGuardian: registration.value.isGuardian,
        currentStep: step.value,
        registration: registration.value,
        onReset: resetRegistration
    };

    const componentSpecificProps = {
        [STEPS.SELECT_NAME]: {
            phone: registration.value.phone,
            email: registration.value.email,
            profiles: registration.value.data,
            user: registration.value.selectedProfile
        },
        [STEPS.GUARDIAN_SELECT]: {
            profiles: registration.value.data,
            user: registration.value.guardian
        },
        [STEPS.PASSWORD_CHECK]: {
            selected: registration.value.selectedProfile
        },
        [STEPS.SUMMARY_AGREEMENT]: {
            user: registration.value.selectedProfile || registration.value.newUser,
            isActivation: registration.value.isRegisteringAsGuardian,
            isGuardian: registration.value.isRegisteringAsGuardian,
            isNewUser: !!registration.value.newUser,
            registration: registration.value
        },
        [STEPS.SELECT_CREATION]: {
            user: registration.value.selectedProfile || registration.value.newUser,
            canBeGuardian: calculateAge(registration.value.birthday) >= 18
        },
        [STEPS.SHOW_QR]: {
            user: registration.value.selectedProfile || registration.value.newUser
        },
        [STEPS.EMAIL_SETUP]: {
            user: registration.value.selectedProfile || registration.value.newUser
        },
        [STEPS.SET_PASSWORD]: {}
    };

    return {
        ...baseProps,
        ...(componentSpecificProps[step.value] || {})
    };
});

// Lag en felles aktiveringsmetode
const activateUser = async (userData) => {
    try {
        const response = await axios.post('/api/users/activate', {
            user_id: userData.user_id,
            email: userData.email,
            password: userData.password,
            terms_accepted: userData.terms_accepted,
            is_guardian: userData.is_guardian
        });

        await handleLogin({
            email: userData.email,
            password: userData.password
        });

        return response.data;
    } catch (error) {
        console.error('Feil ved aktivering/innlogging:', error);
        registration.value.errors = error.response?.data?.errors;
        throw error;
    }
};

// Oppdater saveAndLogin og saveGuardianAndChild
const saveAndLogin = async () => {
    await activateUser({
        user_id: registration.value.selectedProfile.id,
        email: registration.value.email,
        password: registration.value.password,
        terms_accepted: true,
        is_guardian: registration.value.isRegisteringAsGuardian
    });
};

const encryptPassword = async (password) => {
    try {
        // Hent krypteringsnøkkel
        const response = await axios.get('/api/keys/encryption');
        const key = response.data.key;

        // Krypter passordet med AES
        return CryptoJS.AES.encrypt(password, key).toString();
    } catch (error) {
        console.error('Krypteringsfeil:', error);
        throw error;
    }
};

const resetRegistration = () => {
    console.log('Starting reset. Current step:', step.value);

    // Sett step først for å unngå undefined state
    step.value = STEPS.PHONE;

    // Reset step history
    prev.value = [];

    // Reset registration data
    registration.value = {
        errors: {},
        data: null,
        phone: null,
        email: null,
        selectedProfile: null,
        newUser: {
            given_name: null,
            additional_name: null,
            family_name: null,
            phone: null,
            email: null
        },
        guardian: null,
        birthday: null,
        isGuardian: false,
        termsAccepted: false,
        isRegisteringAsGuardian: false,
    };

    console.log('Reset complete. New step:', step.value);
};

// Fjern handleReset siden vi bruker resetRegistration direkte

// I template, bruk resetRegistration direkte
</script>

