<template>
    <div class="p-4 dark:bg-gray-900 dark:text-white">
        <!-- Overskrift -->
        <h1 class="text-xl font-bold mb-4">{{ trans('auth.birth_title') }}</h1>

        <form>
            <!-- Fødselsdag -->
            <div class="mb-4">
                <label for="birthday" class="block text-sm font-medium mb-1">
                    {{ trans('auth.birthday') }}<span class="text-red-500 pl-2">*</span>
                </label>
                <div class="flex items-center">
                    <input
                        id="birthday"
                        type="date"
                        v-model="birthDate"
                        :min="minDate"
                        :max="maxDate"
                        class="w-full p-2 border border-gray-300 rounded dark:border-gray-700 dark:bg-gray-800"
                        @change="calculateAge"
                        :class="{' dark:border-red-500 border-red-500' : errorField === 'birthDate'}"
                    />
                    <div class="ml-4 text-sm" v-if="age !== null">
                        {{ trans('auth.age') }}: <strong>{{ age }}</strong>
                    </div>
                </div>
            </div>

            <!-- PEGI- og ESRB-symboler -->
            <div class="mb-4">
                <h2 class="text-lg font-semibold mb-2">{{ trans('auth.available_ratings') }}</h2>

                <div class="w-full">
                    <!-- PEGI-symboler -->
                    <div class="flex columns-5 items-center justify-evenly">
                        <div
                            v-for="(pegi, index) in filteredPegiRatings"
                            :key="'pegi-' + index"
                            class="flex flex-col items-center p-2"
                        >
                            <img :src="pegi.image" :alt="pegi.label" class="h-14"/>
                            <span class="text-xs mt-1">{{ pegi.label }}</span>
                        </div>
                    </div>

                    <!-- ESRB-symboler -->
                    <div class="columns-5 items-center flex justify-evenly">
                        <div
                            v-for="(esrb, index) in filteredEsrbRatings"
                            :key="'esrb-' + index"
                            class="flex flex-col items-center p-2"
                        >
                            <img :src="esrb.image" :alt="esrb.label" class="h-14"/>
                            <span class="text-xs mt-1">{{ esrb.label }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Postnummer -->
            <div class="mb-4">
                <label for="postcode" class="block text-sm font-medium mb-1">
                    {{ trans('auth.postcode') }}<span class="text-red-500 pl-2">*</span>
                </label>
                <input
                    id="postcode"
                    type="text"
                    v-model="postcode"
                    class="w-full p-2 border border-gray-300 rounded dark:border-gray-700 dark:bg-gray-800"
                    :class="{' dark:border-red-500 border-red-500' : errorField === 'postcode'}"
                />
            </div>
            <!-- Feilmelding -->
            <p v-if="errorMessage" class="mt-4 text-center text-red-500">
                {{ errorMessage }}
            </p>

            <!-- Knappene -->
            <div class="flex justify-between mt-6">
                <BackButton @goBack="emits('goBack')" />
                <NextButton @goNext="goNext" />
            </div>
        </form>
    </div>
</template>

<script setup>
import {computed, ref} from "vue";
import {trans} from "laravel-vue-i18n";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import BackButton from "../utils/BackButton.vue";
import NextButton from "../utils/NextButton.vue";

const emits = defineEmits(['goNext', 'goBack']);

// Reactive Verdier
const birthDate = ref(''); // Fødselsdato input
const postcode = ref(''); // Postnummer
const age = ref(null); // Beregnet alder
const errorMessage = ref('');
const errorField = ref('');

function goNext() {
    errorMessage.value = '';
    errorField.value = '';
    if (birthDate.value === '') {
        errorMessage.value = trans('auth.birth_error');
        errorField.value = 'birthDate';
        return;
    }
    if (postcode.value === '') {
        errorMessage.value = trans('auth.postcode_error');
        errorField.value = 'postcode';
        return;
    }
    emits('goNext', {birthDate: birthDate.value, postcode: postcode.value,age: age.value});
}

// Funksjon: Beregn alder basert på fødselsdato
const calculateAge = () => {
    if (!birthDate.value) {
        age.value = null;
        return;
    }

    const today = new Date();
    const birth = new Date(birthDate.value);
    let computedAge = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();

    // Sjekk om brukeren ikke har hatt bursdag i år ennå
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        computedAge--;
    }

    age.value = computedAge;
};

// Dynamiske grenser for fødselsdato
const today = new Date();
const minDate = ref(new Date(today.getFullYear() - 90, today.getMonth(), today.getDate()).toISOString().split("T")[0]); // Maks 90 år gammel
const maxDate = ref(new Date(today.getFullYear() - 10, today.getMonth(), today.getDate()).toISOString().split("T")[0]); // Minst 10 år gammel

// PEGI-symboler: Label, bilde, og aldersgrupper
const pegiRatings = [
    {label: "3+", image: "/images/pegi/age-3.jpg", minAge: 3, maxAge: 6},
    {label: "7+", image: "/images/pegi/age-7.jpg", minAge: 7, maxAge: 11},
    {label: "12+", image: "/images/pegi/age-12.jpg", minAge: 12, maxAge: 15},
    {label: "16+", image: "/images/pegi/age-17.jpg", minAge: 16, maxAge: 17},
    {label: "18+", image: "/images/pegi/age-18.jpg", minAge: 18, maxAge: 90},
];

// ESRB-symboler: Label, bilde, og aldersgrupper
const esrbRatings = [
    {label: trans("Everyone"), image: "/images/esrb/E.svg", minAge: 3, maxAge: 9},
    {label: trans("Everyone 10+"), image: "/images/esrb/E10plus.svg", minAge: 10, maxAge: 12},
    {label: trans("Teen"), image: "/images/esrb/T.svg", minAge: 13, maxAge: 16},
    {label: trans("Mature 17+"), image: "/images/esrb/M.svg", minAge: 17, maxAge: 17},
    {label: trans("Adults Only"), image: "/images/esrb/AO.svg", minAge: 18, maxAge: 90},
];

// Filtrer symboler basert på aldersinndata
const filteredPegiRatings = computed(() => {
    if (age.value === null) {
        return [pegiRatings[0]]; // Vis kun 3+ hvis ingen alder
    }
    return pegiRatings.filter((pegi) => pegi.minAge <= age.value);
});

const filteredEsrbRatings = computed(() => {
    if (age.value === null) {
        return [esrbRatings[0]]; // Vis kun E hvis ingen alder
    }
    return esrbRatings.filter((esrb) => esrb.minAge <= age.value);
});

// Marker aktive PEGI- og ESRB-symboler basert på aldersgrense
const isPegiActive = (pegi) => {
    return age.value !== null && pegi.minAge <= age.value && pegi.maxAge >= age.value;
};

const isEsrbActive = (esrb) => {
    return age.value !== null && esrb.minAge <= age.value && esrb.maxAge >= age.value;
};
</script>
