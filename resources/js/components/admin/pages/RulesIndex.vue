<template>
    <div class="ml-4">
        <div class="grid grid-cols-2">
            <h1 class="row-start mr-2 text-4xl font-bold">Rules:</h1>
            <div>
                <button
                    @click="newRule"
                    class="row-end text-white bg-yellow-600 hover:bg-yellow-800 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-base items-center px-3 py-2 text-center mr-2"
                >
                    New rule
                </button>
            </div>
        </div>
        <div class="mt-6" v-for="([index, location]) in Object.entries(rules)" :key="index">
            <h2 class="text-2xl font-bold">{{ location.location_name }}</h2>
            <div
                class="border-t mt-3 pt-3 mb-6"
                v-for="rule in location.rules"
                :key="rule.id"
                @click="editRule(rule.id)"
            >
                <div>
          <span v-if="rule.pending" class="mr-1">
            <font-awesome-icon :icon="['fas','clock']" class="text-orange-500"></font-awesome-icon>
          </span>
                    <span v-if="rule.active" class="mr-1">
            <font-awesome-icon :icon="['fas','check']" class="text-green-500"></font-awesome-icon>
          </span>
                    <span v-if="rule.deleted_at" class="mr-1">
            <font-awesome-icon :icon="['fas','trash']" class="text-red-500"></font-awesome-icon>
          </span>
                    <span class="font-bold">{{ rule.name }}</span>
                </div>
                <div>{{ rule.description }}</div>
            </div>
        </div>
    </div>
    <create-rule :modalOpen="modalOpen" :edit="edit" @closeModal="closeModal" @success="successRule"></create-rule>
</template>

<script setup>
import {ref, onMounted} from 'vue';
import axios from 'axios';
import CreateRule from '../modals/CreateRule.vue';
import {notify} from 'notiwind';
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';

const props = defineProps({
    me: Object,
});

const rules = ref([]);
const modalOpen = ref(false);
const edit = ref(null);

const fetch = async () => {
    try {
        const res = await axios.get('/api/admin/rules');
        console.log('Fetched rules:', res.data);
        rules.value = Object.values(res.data);
    } catch (err) {
        console.error('Error fetching rules:', err);
        notify({group: 'error', title: 'Error', text: 'Failed to fetch rules'});
    }
};

const editRule = (id) => {
    for (const location of rules.value) {
        for (const rule of location.rules) {
            if (rule.id === id) {
                edit.value = rule;
                modalOpen.value = true;
                return;
            }
        }
    }
};

const newRule = () => {
    edit.value = null;
    modalOpen.value = true;
};

const closeModal = () => {
    edit.value = null;
    modalOpen.value = false;
};

const successRule = (value) => {
    const existingRuleIndex = rules.value.findIndex(location =>
        location.rules.findIndex(rule => rule.id === value.id) !== -1
    );

    if (existingRuleIndex !== -1) {
        const location = rules.value[existingRuleIndex];
        const ruleIndex = location.rules.findIndex(rule => rule.id === value.id);
        location.rules.splice(ruleIndex, 1, value); // Update rule if editing
    } else {
        rules.value.push(value); // Add new rule (if needed)
    }
    closeModal();
    notify({group: 'success', title: 'Successful', text: edit.value ? 'Rule updated' : 'Added new rule'}, 4000);
};

onMounted(() => {
    fetch();
});
</script>
