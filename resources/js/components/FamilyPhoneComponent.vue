<template>
    <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
        Family phone number <span class="text-red-700">*</span>
    </label>
    <!-- INPUT TEXT PHONE -->
    <input
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        id="phone" type="text" placeholder="Phone" v-model="phone" required :class="{'border-red-700':hasErrors.phone}"
        v-on:focus="$emit('sendErrors',{phone: false})">
    <span class="m-5 block text-red-700" v-if="hasErrors.phone">{{ hasErrors.phone }}</span>
    <ButtonBar :next="true" @go="finder" :required="true" @close="$emit('close')"></ButtonBar>
</template>
<script>
import ButtonBar from "./ButtonBar.vue";
import axios from "axios";

export default {
    components: {ButtonBar},
    props: {
        hasErrors: {
            type: Object,
            required: true
        },
    },
    emits: ['sendErrors', 'close', 'familyPhone'],
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
                    this.$emit('familyPhone', {data: 1, phone: this.phone});
                } else {
                    this.$emit('familyPhone', {data: response.data, phone: this.phone});
                }
            });
        }
    }
}
</script>
