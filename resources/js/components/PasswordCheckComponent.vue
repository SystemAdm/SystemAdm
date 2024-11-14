<template>
    <div class="mb-5">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
            Password <span class="text-red-700">*</span>
            <span
                class="block text-gray-400 text-sm">This account is protected by a password</span>
        </label>
        <!-- INPUT TEXT PHONE -->
        <input
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            id="password"
            type="password"
            placeholder="Password"
            v-model="password"
            required
            :class="{'border-red-700':hasErrors.password}"
            v-on:focus="sendErrors({password: false})">
        <span class="ml-3 mt-2 block text-red-700" v-if="hasErrors.password">{{ hasErrors.password }}</span>
    </div>
    <ButtonBar :next="true" @close="$emit('close')" @go="check" @back="back"></ButtonBar>
</template>
<script>
import axios from "axios";
import ButtonBar from "./ButtonBar.vue";

export default {
    components: {ButtonBar},
    props: {
        hasErrors: Object,
        user: {
            type: Object,
            required: true
        },
    },
    emits: ['sendErrors', 'close', 'back', 'success'],
    data() {
        return {
            password: null,
        }
    },
    methods: {
        sendErrors(value) {
            this.$emit('sendErrors', value);
        },
        check() {
            if (this.password == null) {
                this.sendErrors({password: 'A password must be given'});
                return;
            }
            axios.post('/api/users/check', {p: this.password,u:this.user.id}).then(response => {
                if (response.data === true) {
                    console.log('ha');
                    this.$emit('success', true);
                } else {
                    this.sendErrors({password: 'Incorrect password'});
                }
            });
        },
        back() {

        }
    }
}
</script>
