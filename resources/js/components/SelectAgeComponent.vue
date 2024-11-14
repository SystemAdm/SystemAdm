<template>
    <label class="block text-gray-700 text-sm font-bold mb-2" for="lastname">
        <span class="block text-gray-400 text-sm">If not set, you will be treated as less than 13 years old</span>
    </label>
    <!-- INPUT TEXT PHONE -->
    <input
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        id="birthday"
        type="date"
        :max="minDate"
        placeholder="Birthday"
        v-model="birthday"
        :class="{'border-red-700':hasErrors.birthday}"
        v-on:change="setAge"
        v-on:focus="sendErrors({birthday: false})">
    <span class="mt-2 gap-5 inline-flex"><PegiComponent :age="age"></PegiComponent><EsrbComponent :age="age"></EsrbComponent></span>
    <span class="ml-3 mt-2 block text-red-700" v-if="hasErrors.birthday">{{ hasErrors.birthday }}</span>
    <ButtonBar :next="true" @go="birth"></ButtonBar>
</template>
<script>
import {DateTime} from "luxon";
import ButtonBar from "./ButtonBar.vue";
import PegiComponent from "./PegiComponent.vue";
import EsrbComponent from "./EsrbComponent.vue";

export default {
    components: {EsrbComponent, PegiComponent, ButtonBar},
    emits: ['sendErrors', 'birth','close'],
    props: {
        hasErrors: Object,
    },
    data() {
        return {
            minDate: '',
            birthday: null,
            age:3,
        }
    },
    methods: {
        sendErrors(value) {
            this.$emit('sendErrors', value);
        },
        birth() {
            this.$emit('birth', this.birthday);
        },
        setAge() {
            let birth = DateTime.fromISO(this.birthday);
            if (!birth.isValid) {
                this.age = 3;
                return;
            }
            let today = DateTime.now();
            this.age = Math.max(Math.floor(today.diff(birth,'years').years),3);
        }
    },
    mounted() {
        this.minDate = DateTime.now().minus({years: 10}).toISODate();
    },
}
</script>
