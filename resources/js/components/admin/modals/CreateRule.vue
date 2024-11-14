<template>
    <div v-if="modalOpen" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full p-4">
        <div class="relative shadow-xl rounded-md bg-white text-black">

            <div class="flex justify-between p-2">
                <h1 class="p-3 font-bold text-3xl">Create rule</h1>
                <button @click="closeModal" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-3xl p-1.5 ml-auto inline-flex items-center">
                    &times;
                </button>
            </div>
            <div class="p-6 pt-0 text-center">
                <label class="font-bold">Name:</label>
                <input v-model="name"
                       class="my-2 shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline">
                <label class="font-bold">Description:</label>
                <textarea v-model="description"
                          class="my-2 shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                <div class="m-3">
                    <div class="inline-flex items-center mx-2">
                        <label class="flex items-center cursor-pointer relative" for="check-pending">
                            <input type="checkbox"
                                   v-model="this.pending"
                                   class="peer h-5 w-5 cursor-pointer transition-all rounded shadow hover:shadow-md border border-slate-300 checked:bg-slate-800 checked:border-slate-800"
                                   id="check-pending"/>
                            <span
                                class="absolute text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"></span>
                        </label>
                        <label class="cursor-pointer ml-2 text-slate-600 text-sm" for="check-check"
                               :class="{'text-black':pending}">
                            Newly changed
                        </label>
                    </div>
                    <div class="inline-flex items-center mx-2">
                        <label class="flex items-center cursor-pointer relative" for="check-2">
                            <input type="checkbox"
                                   v-model="this.active"
                                   class="peer h-5 w-5 cursor-pointer transition-all rounded shadow hover:shadow-md border border-slate-300 checked:bg-slate-800 checked:border-slate-800"
                                   id="check-active"/>
                            <span
                                class="absolute text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"></span>
                        </label>
                        <label class="text-black cursor-pointer ml-2  text-sm" for="check-2">
                            Activated
                        </label>
                    </div>
                </div>
                <ButtonBar :delete="true" :trashed="edit.deleted_at" :submit="true" @go="submitRule"
                           @close="closeModal"></ButtonBar>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import ButtonBar from "../../ButtonBar.vue";

export default {
    components: {ButtonBar},
    props: {
        modalOpen: {
            type: Boolean,
            required: true
        },
        edit: {
            type: Object,
            required: false
        }
    },
    data() {
        return {
            name: null,
            description: null,
            pending: false,
            active: false,
            initialPending: false,
        }
    },
    emits: ['closeModal', 'success', 'update'],
    methods: {
        submitRule() {
            if (this.edit) {
                const originalRule = this.edit;
                if (
                    (this.pending === false && this.initialPending === true) ||
                    this.name !== originalRule.name ||
                    this.description !== originalRule.description
                ) {
                    this.pending = true;
                }
            }

            const payload = {
                name: this.name,
                description: this.description,
                pending: this.pending,
                active: this.active
            };
            const url = this.edit ? `/api/admin/rules/${this.edit.id}` : '/api/admin/rules';
            const method = this.edit ? 'put' : 'post';

            axios({method, url, data: payload})
                .then(response => {
                    this.resetting();
                    this.$emit('success', response.data);
                });
        },
        resetting(){
            this.name = null;
            this.description = null;
            this.active = false;
            this.initialPending = false;
            this.pending = false;
        },
        closeModal() {
            this.resetting();
            this.$emit('closeModal');
        }
    },
    watch: {
        edit: {
            immediate: true,
            handler(val) {
                if (val) {
                    this.name = val.name;
                    this.description = val.description;
                    this.pending = Boolean(this.edit?.pending ?? false);
                    this.active = Boolean(this.edit?.active ?? false);
                    this.initialPending = val.pending
                } else {
                    this.name = null;
                    this.description = null;
                    this.pending = false;
                    this.active = false;
                    this.initialPending = false;
                }
            },
        },
    },
}
</script>
