<template>
    <div class="max-w-2xl mx-auto text-black">
        <h2 class="text-xl font-bold mb-4">{{ $t('common.summary') }}</h2>

        <!-- Brukerinfo seksjon -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <h3 class="font-semibold mb-2">{{ $t('common.your_information') }}</h3>
            <dl class="space-y-2">
                <div class="flex">
                    <dt class="w-1/3 text-gray-600">{{ $t('common.name') }}:</dt>
                    <dd class="w-2/3">{{ user.given_name }} {{ user.family_name }}</dd>
                </div>
                <div class="flex">
                    <dt class="w-1/3 text-gray-600">{{ $t('common.phone') }}:</dt>
                    <dd class="w-2/3">{{ registration.phone }}</dd>
                </div>
            </dl>
        </div>

        <!-- Foresatt seksjon (som egen seksjon) -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold">{{ $t('common.guardian_section') }}</h3>
                <button
                    v-if="!registration.guardian && (isAgeValid || !registration.guardian)"
                    @click="addGuardian"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100"
                >
                    <font-awesome-icon :icon="['fas', 'plus']" class="mr-2"/>
                    {{ $t('common.add_guardian') }}
                </button>
            </div>

            <!-- Vis foresatt info hvis den finnes -->
            <template v-if="registration.guardian">
                <dl class="space-y-2">
                    <div class="flex">
                        <dt class="w-1/3 text-gray-600">{{ $t('common.name') }}:</dt>
                        <dd class="w-2/3">
                            {{ registration.guardian.given_name }} {{ registration.guardian.family_name }}
                            <span class="text-gray-500 text-sm">({{
                                    $t(`common.relations.${registration.guardian.relation}`)
                                }})</span>
                        </dd>
                    </div>
                    <div class="flex">
                        <dt class="w-1/3 text-gray-600">{{ $t('common.phone') }}:</dt>
                        <dd class="w-2/3">{{ registration.guardian.phone }}</dd>
                    </div>
                </dl>
            </template>

            <!-- Vis melding basert på alder -->
            <div v-else class="text-sm" :class="!isAgeValid ? 'text-red-600' : 'text-gray-600'">
                {{
                    !isAgeValid
                        ? $t('common.guardian_required_message')
                        : $t('common.guardian_optional_message')
                }}
            </div>
        </div>

        <!-- Retningslinjer -->
        <div class="bg-white p-4 rounded-lg border mb-6">
            <h3 class="font-semibold mb-2">{{ $t('common.guidelines') }}</h3>
            <ul class="list-disc pl-5 space-y-2 text-sm">
                <li>{{ $t('guidelines.phone_truthful') }}</li>
                <li>{{ $t('guidelines.name_truthful') }}</li>
                <li>{{ $t('guidelines.age_truthful') }}</li>
                <li>{{ $t('guidelines.guardian_truthful') }}</li>
            </ul>
        </div>

        <!-- Privacy Notice godkjenning -->
        <div class="mb-6">
            <label class="flex items-start space-x-2 cursor-pointer">
                <input
                    type="checkbox"
                    v-model="privacyAccepted"
                    class="mt-1"
                    required
                >
                <span class="text-sm">
                    {{ $t('common.privacy_acceptance') }}
                    <button
                        @click="showPrivacyNotice"
                        class="text-blue-600 hover:underline"
                    >
                        {{ $t('common.privacy_notice') }}
                    </button>
                </span>
            </label>
        </div>

        <PrivacyNoticeComponent/>

        <div class="consent-checkbox">
            <input
                type="checkbox"
                v-model="hasAccepted"
                id="gdpr-consent"
            >
            <label for="gdpr-consent">
                {{ $t('privacy.consent_text') }}
            </label>
        </div>

        <ButtonBar
            :prev="prev"
            :hasNext="true"
            :submit="true"
            :current-step="currentStep"
            :disabled="!privacyAccepted"
            @go="handleSubmit"
            @back="$emit('back')"
        />
    </div>
</template>

<script setup>
import {computed, ref} from 'vue';
import ButtonBar from './ButtonBar.vue';
import PrivacyNoticeComponent from './PrivacyNoticeComponent.vue'
import {calculateAge} from './utils/age.js';

const privacyAccepted = ref(false);
const hasAccepted = ref(false);

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
    registration: {
        type: Object,
        required: true
    },
    isActivation: {
        type: Boolean,
        default: false
    },
    isGuardian: {
        type: Boolean,
        default: false
    },
    isNewUser: {
        type: Boolean,
        default: false
    },
    prev: {
        type: Array,
        required: true
    },
    STEPS: {
        type: Object,
        required: true
    },
    currentStep: {
        type: Number,
        required: true
    },
    hasErrors: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['back', 'success']);

const showPrivacyNotice = () => {
    emit('back', props.STEPS.PRIVACY_NOTICE);
};

const handleSubmit = async () => {
    if (!privacyAccepted.value) return;
    emit('success', {agreed: true});
};

// Beregn om alderen er gyldig (over 18)
const isAgeValid = computed(() => {
    return calculateAge(props.registration.birthday) >= 18;
});

// Håndter klikk på "Legg til foresatt"
const addGuardian = () => {
    emit('back', props.STEPS.GUARDIAN_PHONE);
};
</script>
