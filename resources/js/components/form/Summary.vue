<template>
    <div class="p-4 dark:bg-gray-900 dark:text-white">
        <!-- Overskrift -->
        <h1 class="text-xl font-bold mb-4">{{ trans('auth.summary_title') }}</h1>

        <!-- Brukerinformasjon -->
        <section class="mb-4" v-if="createUser">
            <h2 class="text-lg font-semibold mb-2">{{ trans('auth.user_info') }}</h2>
            <div class="bg-gray-50 p-4 border border-gray-300 rounded text-sm text-gray-800 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                <ul class="list-none space-y-2 text-sm">
                    <li>
                        <strong>{{ trans('auth.given_name') }}:</strong> {{ createUser.given_name }}
                    </li>
                    <li>
                        <strong>{{ trans('auth.additional_name') }}:</strong> {{ createUser.additional_name || trans('auth.not_provided') }}
                    </li>
                    <li>
                        <strong>{{ trans('auth.family_name') }}:</strong> {{ createUser.family_name }}
                    </li>
                    <li>
                        <strong>{{ trans('auth.' + createUser.inputType) }}:</strong> {{ createUser.input }}
                    </li>
                    <li>
                        <strong>{{ trans('auth.birthday') }}:</strong> {{ createUser.birthday }}
                    </li>
                    <li>
                        <strong>{{ trans('auth.postcode') }}:</strong> {{ createUser.postcode }}
                    </li>
                </ul>
            </div>
        </section>

        <!-- Vergeinformasjon -->
        <section v-if="createGuardian.given_name || createUser.age < 18" class="mb-4">
            <h2 class="text-lg font-semibold mb-2">{{ trans('auth.guardian_info') }}</h2>
            <div class="bg-gray-50 p-4 border border-gray-300 rounded text-sm text-gray-800 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                <ul class="list-none space-y-2">
                    <li>
                        <strong>{{ trans('auth.given_name') }}:</strong> {{ createGuardian.given_name || trans('auth.not_provided') }}
                    </li>
                    <li>
                        <strong>{{ trans('auth.family_name') }}:</strong> {{ createGuardian.family_name || trans('auth.not_provided') }}
                    </li>
                    <li>
                        <strong>{{ trans('auth.input_placeholder') }}:</strong> {{ createGuardian.input || trans('auth.not_provided') }}
                    </li>
                </ul>
            </div>
        </section>

        <!-- GDPR Informasjon -->
        <section class="mb-4">
            <h2 class="text-lg font-semibold mb-2">{{ trans('auth.gdpr_title') }}</h2>
            <div class="bg-gray-50 p-4 border border-gray-300 rounded text-sm text-gray-800 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                {{ trans('auth.gdpr_message') }}
            </div>
        </section>

        <!-- Bekreftelsescheckbox -->
        <div class="flex items-center mt-4">
            <input
                id="confirm-data-save"
                type="checkbox"
                v-model="consentGiven"
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded"
            />
            <label for="confirm-data-save" class="ml-2 text-sm text-gray-900 dark:text-gray-300">
                {{ trans('auth.confirm_gdpr') }}
            </label>
        </div>

        <!-- Neste knapp -->
        <div class="flex justify-between mt-6">
            <button
                @click="emits('goBack')"
                class="py-2 px-4 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700"
            >
                {{ trans('auth.back') }}
            </button>
            <button
                @click="emits('goNext')"
                :disabled="!consentGiven"
                class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded"
            >
                {{ trans('auth.next') }}
            </button>
        </div>
    </div>
</template>

<script setup>
import {onMounted, ref} from "vue";
import {trans} from "laravel-vue-i18n"; // For oversettelser

const createUser = ref({
    given_name: 'Test',
    additional_name: '',
    family_name: 'Testesen',
    inputType: 'phone',
    input: '98218519',
    birthday: '1982-11-02',
    postcode: '3512',
    validated: true,
    age: 13,
});
const createGuardian = ref({
    given_name: 'Test',
    family_name: 'Testesen',
    input: '98218519',
    inputType: 'phone',
})
// Props for informasjon om brukeren og verge
const props = defineProps({
    /*createUser: {
        type: Object,
        //required: true, // Data om brukeren MÅ være tilstede
    },
    createGuardian: {
        type: Object,
        //required: false, // Verge-informasjon er valgfritt
    },*/
});
onMounted(() => {
    console.log(props.createUser);
    console.log(props.createGuardian);
});
const emits = defineEmits(['goNext', 'goBack']);

// Reactive variabel for bekreftelse
const consentGiven = ref(false); // Sjekkboksverdi
</script>
