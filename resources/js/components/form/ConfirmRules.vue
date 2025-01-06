<template>
    <div class="p-4 text-gray-800 bg-white dark:bg-gray-900 dark:text-white">
        <!-- Overskrift -->
        <h1 class="text-xl font-bold mb-4">{{ trans('auth.rules_title') }}</h1>

        <!-- Regler for bruk -->
        <div class="mb-4">
            <div class="text-lg font-semibold mb-2"
                 v-html="trans('auth.rules_subtitle')"></div>

            <div
                v-if="loading"
                class="text-gray-600 dark:text-gray-400"
            >
                {{ trans('auth.loading') }}
            </div>
            <div
                v-else-if="rules.length === 0"
                class="text-gray-600 dark:text-gray-400"
            >
                {{ trans('auth.no_rules') }}
            </div>
            <div v-else class="bg-gray-50 p-4 border border-gray-300 rounded text-sm text-gray-800 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                <ul class="list-disc pl-5 space-y-2">
                    <li v-for="(rule, index) in rules" :key="rule.id">
                        <p class="text-sm font-semibold">{{ rule.name }}</p>
                        <p class="text-xs">{{ rule.description }}</p>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Checkbox for bekreftelse -->
        <div class="flex items-center mt-4">
            <input
                id="confirm-rules"
                type="checkbox"
                v-model="confirmed"
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                :disabled="rules.length === 0"
            />
            <label v-html="trans('auth.rules_checkbox_text')" for="confirm-rules" class="ml-2 text-sm text-gray-900 dark:text-gray-300">
            </label>
        </div>

        <!-- Neste knapp -->
        <div class="flex justify-between mt-6">
            <button
                @click.prevent="emits('goBack')"
                type="button"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700"
            >
                <font-awesome-icon :icon="['fas', 'arrow-left']" class="mr-2"/>
                {{ trans('auth.back') }}
            </button>
            <button
                type="button"
                :disabled="!confirmed || rules.length === 0"
                @click.prevent="emits('goNext')"
                class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {{ trans('auth.next') }}
                <font-awesome-icon :icon="['fas', 'arrow-right']" class="ml-2"/>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { trans } from "laravel-vue-i18n";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome"; // For håndtering av oversettelser
const emits = defineEmits(['goBack','goNext']);
// Reactive Variabler
const rules = ref([]); // Liste med relevante regler fra API
const confirmed = ref(false); // Sjekkboks-status (om bekreftet)
const loading = ref(true); // Lastestatus for API

// Hent regler fra API ved mount
const fetchRules = async () => {
    try {
        const response = await axios.get("/api/rules/0");
        rules.value = response.data; // Lagre regler fra API
    } catch (error) {
        console.error("Error fetching rules:", error); // Feilhåndtering
    } finally {
        loading.value = false; // Sett loading til false uansett resultat
    }
};

// Hent reglene umiddelbart når komponenten mountes
onMounted(() => {
    fetchRules();
});
</script>
