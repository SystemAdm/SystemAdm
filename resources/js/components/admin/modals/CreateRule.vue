<template>
    <div v-if="modalOpen" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full p-4">
        <div class="relative shadow-xl rounded-md bg-white text-black">
            <div class="flex justify-between p-2">
                <h1 class="p-3 font-bold text-3xl">{{t('rules.new_rule')}}</h1>
                <button @click="closeModal" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-3xl p-1.5 ml-auto inline-flex items-center">
                    &times;
                </button>
            </div>
            <div class="p-6 pt-0">
                <label class="font-bold" for="location">{{t('rules.location')}}</label>
                <select id="location" v-model="selectedLocation"
                        class="my-2 shadow border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline">
                    <option v-for="location in locations" :key="location.id" :value="location.id">{{location.name}}</option>
                </select>
                <label class="font-bold">{{ t('rules.title')}}:</label>
                <input v-model="name"
                       class="my-2 shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline">
                <label class="font-bold">{{ t('rules.description')}}:</label>
                <textarea v-model="description"
                          class="my-2 shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                <div class="m-3">
                    <div class="inline-flex items-center mx-2">
                        <label class="flex items-center cursor-pointer relative" for="check-pending">
                            <input type="checkbox"
                                   v-model="pending"
                                   class="peer h-5 w-5 cursor-pointer transition-all rounded shadow hover:shadow-md border border-slate-300 checked:bg-slate-800 checked:border-slate-800"
                                   id="check-pending"/>
                            <span
                                class="absolute text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"></span>
                        </label>
                        <label class="cursor-pointer ml-2 text-slate-600 text-sm" for="check-pending"
                               :class="{'text-black':pending}">
                            {{t('rules.newly_changed')}}
                        </label>
                    </div>
                    <div class="inline-flex items-center mx-2">
                        <label class="flex items-center cursor-pointer relative" for="check-active">
                            <input type="checkbox"
                                   v-model="active"
                                   class="peer h-5 w-5 cursor-pointer transition-all rounded shadow hover:shadow-md border border-slate-300 checked:bg-slate-800 checked:border-slate-800"
                                   id="check-active"/>
                            <span
                                class="absolute text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"></span>
                        </label>
                        <label class="text-black cursor-pointer ml-2 text-sm" for="check-active">
                            {{ t('rules.activated')}}
                        </label>
                    </div>
                </div>
                <ButtonBar :canDelete="true" :trashed="!!edit?.deleted_at" :submit="true" @go="submitRule"
                           @close="closeModal"></ButtonBar>
            </div>
        </div>
    </div>
</template>

<script setup>
import {defineEmits, defineProps, onMounted, ref, watch} from 'vue';
import axios from 'axios';
import ButtonBar from '../../ButtonBar.vue';
import {trans} from 'laravel-vue-i18n';

const t = trans;

const props = defineProps({
    modalOpen: {
        type: Boolean,
        required: true
    },
    edit: {
        type: Object,
        required: false
    }
});

const emit = defineEmits(['closeModal', 'success', 'update']);

const name = ref(null);
const description = ref(null);
const pending = ref(false);
const active = ref(false);
const initialPending = ref(false);
const locations = ref([]);
const selectedLocation = ref(null);

const resetForm = () => {
    selectedLocation.value = null;
    name.value = null;
    description.value = null;
    active.value = false;
    initialPending.value = false;
    pending.value = false;
};

const closeModal = () => {
    resetForm();
    emit('closeModal');
};

const submitRule = () => {
    if (props.edit) {
        const originalRule = props.edit;
        if (
            (pending.value === false && initialPending.value === true) ||
            name.value !== originalRule.name ||
            description.value !== originalRule.description
        ) {
            pending.value = true;
        }
    }

    const payload = {
        name: name.value,
        description: description.value,
        pending: pending.value,
        active: active.value
    };
    const url = props.edit ? `/api/admin/rules/${props.edit.id}` : '/api/admin/rules';
    const method = props.edit ? 'put' : 'post';

    axios({method, url, data: payload})
        .then(response => {
            resetForm();
            emit('success', response.data);
        });
};

onMounted(() => {
    axios.get('/api/locations')
        .then(response => {
            locations.value = response.data;
        });
});

watch(() => props.edit, (val) => {
    if (val) {
        selectedLocation.value = val.location_id;
        name.value = val.name;
        description.value = val.description;
        pending.value = Boolean(val.pending);
        active.value = Boolean(val.active);
        initialPending.value = val.pending;
    } else {
        resetForm();
    }
}, {immediate: true});
</script>
