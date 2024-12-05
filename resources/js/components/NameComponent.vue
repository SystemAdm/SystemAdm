<template>
    <div>
        <NameInput
            id="given_name"
            v-model="given_name"
            :has-error="hasErrors.given_name"
            required
            @send-errors="sendErrors"
        />

        <NameInput
            id="additional_name"
            v-model="additional_name"
            :has-error="hasErrors.additional_name"
            @send-errors="sendErrors"
        />

        <NameInput
            id="family_name"
            v-model="family_name"
            :has-error="hasErrors.family_name"
            required
            @send-errors="sendErrors"
        />

        <div v-if="isGuardian" class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">
                {{ $t('common.guardian_relation') }} <span class="text-red-500">*</span>
            </label>
            <select
                v-model="selectedRelation"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                required
            >
                <option value="">{{ $t('common.select_relation') }}</option>
                <option v-for="relation in guardianRelations" :key="relation" :value="relation">
                    {{ $t(`common.relations.${relation}`) }}
                </option>
            </select>
        </div>

        <ButtonBar
            :prev="prev"
            :hasNext="true"
            :disabled="isGuardian && !selectedRelation"
            :current-step="currentStep"
            @close="handleClose"
            @go="validateAndContinue"
            @back="back"
        />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import ButtonBar from "./ButtonBar.vue";
import NameInput from "./fields/NameInput.vue";
import { trans } from 'laravel-vue-i18n'

const props = defineProps({
    prev: {
        type: Array,
        required: true
    },
    hasErrors: {
        type: Object,
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
    isGuardian: {
        type: Boolean,
        required: true
    }
});

const emit = defineEmits(['sendErrors', 'success', 'close', 'back']);

const given_name = ref(null);
const additional_name = ref(null);
const family_name = ref(null);
const selectedRelation = ref('');

const guardianRelations = [
    'mother',
    'father',
    'legal_guardian',
    'grandparent',
    'other'
];

const sendErrors = (value) => {
    emit('sendErrors', value);
};

const validateAndContinue = () => {
    if (!given_name.value || !family_name.value) {
        emit('sendErrors', {
            name: trans('common.validation.name.required')
        });
        return;
    }

    if (props.isGuardian && !selectedRelation.value) {
        emit('sendErrors', {
            relation: trans('common.validation.relation.required')
        });
        return;
    }

    emit('success', {
        given_name: given_name.value,
        additional_name: additional_name.value,
        family_name: family_name.value,
        relation: selectedRelation.value
    });
};

const back = (step) => {
    emit('back', step);
};

const handleClose = () => {
    emit('close')
    emit('back', 1)
}
</script>

<style scoped>
.input-error {
    border-color: rgb(185, 28, 28);
}

.error-message {
    margin-left: 0.75rem;
    margin-top: 0.5rem;
    display: block;
    color: rgb(185, 28, 28);
}

.input-label {
    display: block;
    color: rgb(55, 65, 81);
    font-size: 0.875rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.input-field {
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
    appearance: none;
    border: 1px solid #e5e7eb;
    border-radius: 0.25rem;
    width: 100%;
    padding: 0.5rem 0.75rem;
    color: rgb(55, 65, 81);
    line-height: 1.25;
}

.input-field:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.5);
}
</style>
