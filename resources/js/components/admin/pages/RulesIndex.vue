<template>
    <div class="ml-4">
        <div class="grid grid-cols-2">
            <h1 class="row-start mr-2 text-4xl font-bold">Rules:</h1>
            <div>
            <button @click="newRule"
                    class="row-end text-white bg-yellow-600 hover:bg-yellow-800 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-base items-center px-3 py-2 text-center mr-2 ">
                New rule
            </button>
            </div>
        </div>
        <div class="mt-6" v-for="location in rules" :key="location.name">
            <h2 class="text-2xl font-bold">{{location.location_name}}</h2>
        <div class="border-t mt-3 pt-3 mb-6" v-for="rule in Object.values(location.rules)" :key="rule.id" @click="editRule(rule.id)">
            <div>
                <span v-if="rule.pending" class="mr-1"><FontAwesomeIcon :icon="['fas','clock']"
                                                    class="text-orange-500"></FontAwesomeIcon></span>
                <span v-if="rule.active" class="mr-1"><FontAwesomeIcon :icon="['fas','check']"
                                                    class="text-green-500"></FontAwesomeIcon></span>
                <span v-if="rule.deleted_at" class="mr-1"><FontAwesomeIcon :icon="['fas','trash']"
                                                                       class="text-red-500"></FontAwesomeIcon></span>
                <span class="font-bold">{{ rule.name }}</span>
            </div>
            <div>{{ rule.description }}</div>
        </div>
        </div>
    </div>
    <CreateRule :modalOpen="modalOpen" :edit="edit" @closeModal="closeModal" @success="successRule"></CreateRule>
</template>
<script>
import axios from "axios";
import CreateRule from "../modals/CreateRule.vue";
import {notify} from "notiwind";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

export default {
    components: {FontAwesomeIcon, CreateRule},
    props: {
        me: Object,
    },
    data() {
        return {
            rules: {},
            modalOpen: false,
            edit: null,
        }
    },
    methods: {
        editRule(id) {
            this.edit = this.rules[id];
            this.modalOpen = true;
        },
        newRule() {
            this.edit = null;
            this.modalOpen = true;
        },
        fetch() {
            axios.get("/api/admin/rules").then(res => {
                console.log("Fetched rules:", res.data);
                this.rules = res.data;
            });
        },
        closeModal() {
            this.edit = null;
            this.modalOpen = false;
        },
        successRule(value) {
            if (this.edit) {
                this.rules = { ...this.rules, [value.id]: value }; // Update rule if editing
            } else {
                this.rules = { [value.id]: value, ...this.rules };
            }
            this.closeModal();
            notify({group: 'success', title: 'Successful', text: this.edit ? 'Rule updated' : 'Added new rule'}, 4000);
        }

    },
    mounted() {
        this.fetch();
    }
}
</script>
