<template>
    <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
        Phone number <span class="text-red-700">*</span>
    </label>
    <!-- INPUT TEXT PHONE -->
    <input
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        id="phone" type="text" placeholder="Phone" v-model="phone" required :class="{'border-red-700':hasErrors.phone}"
        v-on:focus="$emit('sendErrors',{phone: false})">
    <span class="m-5 block text-red-700" v-if="hasErrors.phone">{{ hasErrors.phone }}</span>
    <span class="block text-sm hover:cursor-pointer my-2 text-blue-700" @click="$emit('email')">Can I log in with e-mail address instead?</span>
    <ButtonBar :next="true" @go="finder" :required="true" @close="$emit('close')"></ButtonBar>
</template>
<script>
import ButtonBar from "./ButtonBar.vue";
import axios from "axios";

export default {
    components: {ButtonBar},
    emits: ['sendErrors', 'goto', 'selections', 'close','email'],
    props: {
        hasErrors: {
            type: Object,
        }
    },
    data() {
        return {
            phone: null,
        }
    },
    methods: {
        finder() {
            axios.post('/api/users/validate_phone', {phone: this.phone}).then(response => {
                if (response.data === 0) {
                    this.$emit('sendErrors', {phone: "Invalid phone number"});
                } else if (response.data === 1) {
                    this.$emit('selections', {data: 1, phone: this.phone});
                } else {
                    this.$emit('selections', {data: response.data, phone: this.phone});
                }
            });
        }
    }
}
</script>
