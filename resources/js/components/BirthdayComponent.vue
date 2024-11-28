<template>
    <div>
        <DateInput
            id="birthdate"
            v-model="registration.birthday"
            :error="hasErrors.birthdate"
            label-key="common.birthdate"
            required
            @clear-error="clearError"
            @change="updateAge"
        />

        <!-- Aldersgrenser -->
        <div v-if="calculatedAge" class="mt-5 text-gray-700">
            <label class="block text-gray-700 text-sm font-bold">
                {{ $t('common.age_rating') }}
            </label>
            <div class="grid grid-cols-2 gap-4 mb-3">
                <!-- PEGI -->
                <div>
                    <h3 class="text-sm font-semibold mb-2">PEGI</h3>
                    <div class="flex justify-center">
                        <PegiComponent 
                            :age="calculatedAge"
                            :size="56"
                        />
                    </div>
                </div>

                <!-- ESRB -->
                <div>
                    <h3 class="text-sm font-semibold mb-2">ESRB</h3>
                    <div class="flex justify-center">
                        <EsrbComponent 
                            :age="calculatedAge"
                            :size="56"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Vis informasjon og handling for foresatt basert på alder -->
        <div v-if="calculatedAge" class="my-4">
            <!-- Under 18 - Påkrevd foresatt -->
            <div v-if="calculatedAge < 18" class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <font-awesome-icon :icon="['fas', 'triangle-exclamation']" class="text-red-600" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            {{ $t('common.guardian_required') }}
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            {{ $t('common.guardian_required_description') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Over 18 - Valgfri foresatt -->
            <div v-else class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex items-start justify-between">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <font-awesome-icon :icon="['fas', 'info-circle']" class="text-gray-600" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-600">
                                {{ $t('common.guardian_optional') }}
                            </p>
                        </div>
                    </div>
                    <button 
                        @click="addGuardian"
                        class="ml-3 inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100"
                    >
                        <font-awesome-icon :icon="['fas', 'plus']" class="mr-2" />
                        {{ $t('common.add_guardian') }}
                    </button>
                </div>
            </div>
        </div>

        <ButtonBar 
            :prev="prev" 
            :next="true"
            :current-step="currentStep"
            @close="handleClose" 
            @go="validateAndContinue" 
            @back="handleBack"
            @reset="$emit('reset')"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { trans } from 'laravel-vue-i18n'
import ButtonBar from "./ButtonBar.vue";
import DateInput from "./fields/DateInput.vue";
import PegiComponent from "./PegiComponent.vue";
import EsrbComponent from "./EsrbComponent.vue";
import { calculateAge } from './utils/age.js';

const props = defineProps({
    prev: {
        type: Array,
        required: true
    },
    hasErrors: {
        type: Object,
        required: true
    },
    currentStep: {
        type: Number,
        required: true
    },
    registration: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['sendErrors', 'success', 'close', 'back', 'reset']);

const calculatedAge = ref(null);

const clearError = () => {
    emit('sendErrors', { birthdate: false });
};

const handleClose = () => {
    emit('close');
};

const handleBack = (step) => {
    emit('back', step);
};

const updateAge = () => {
    if (!props.registration.birthday) return;
    calculatedAge.value = calculateAge(props.registration.birthday);
};

const validateAndContinue = () => {
    if (!props.registration.birthday) {
        emit('sendErrors', { 
            birthdate: trans('common.validation.birthdate.required') 
        });
        return;
    }

    emit('success', { 
        birthday: props.registration.birthday,
        age: calculatedAge.value
    });
};

const addGuardian = () => {
    if (!props.registration.birthday) {
        emit('sendErrors', { 
            birthdate: trans('common.validation.birthdate.required') 
        });
        return;
    }
    
    emit('success', { 
        birthday: props.registration.birthday,
        age: calculatedAge.value,
        addGuardian: true
    });
};

// Oppdater alderen når komponenten mountes hvis det finnes en lagret dato
onMounted(() => {
    if (props.registration.birthday) {
        updateAge();
    }
});
</script>
