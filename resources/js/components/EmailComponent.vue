<template>
    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
        E-mail address <span class="text-red-700">*</span>
        <span class="block text-gray-400 text-sm">We will only send you critical, but insensitive information. Ex. reset password</span>
    </label>
    <!-- INPUT TEXT PHONE -->
    <input
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        id="email"
        type="email"
        placeholder="E-mail address"
        v-model="this.email"
        required
        :class="{'border-red-700':hasErrors.email}"
        v-on:focus="sendErrors({email: false})">
    <span class="ml-3 mt-2 block text-red-700" v-if="hasErrors.email">{{ hasErrors.email }}</span>
    <label class="block text-gray-700 text-sm font-bold mb-2 mt-5" for="email_confirm">
        E-mail address confirmation<span class="text-red-700">*</span>
        <span
            class="block text-gray-400 text-sm">Please confirm you e-mail address by retyping it</span>
    </label>
    <!-- INPUT TEXT PHONE -->
    <input
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        id="email_confirm"
        type="email"
        placeholder="E-mail address confirmation"
        v-model="this.email_confirm"
        required
        :class="{'border-red-700':hasErrors.email_confirm}"
        v-on:focus="sendErrors({email_confirm: false})">
    <span class="ml-3 mt-2 block text-red-700" v-if="hasErrors.email_confirm">{{
            hasErrors.email_confirm
        }}</span>
    <span class="block text-sm hover:cursor-pointer my-2 text-blue-700" @click="$emit('phone')">Can I log in with phone number instead?</span>
    <ButtonBar :next="true" @go="next" :required="true"></ButtonBar>
</template>
<script>
import ButtonBar from "./ButtonBar.vue";

export default {
    components: {ButtonBar},
    props: {
        hasErrors: Object,
    },
    emits:['sendErrors','close','phone','next'],
    data(){
        return {
            email:null,
            email_confirm: null,
        }
    },
    methods:{
        sendErrors(value){
            this.$emit('sendErrors',value);
        },
        next(){
            if (this.email == null) this.sendErrors({email:'Field is mandatory'});
            if (this.email_confirm == null) this.sendErrors({email_confirm:'Field is mandatory'});
        }
    }
}
</script>
