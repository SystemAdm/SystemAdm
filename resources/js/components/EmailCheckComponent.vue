<template>
    <h2 class="text-3xl" v-if="guardian">Guardian/Parent</h2>
    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
        E-mail address <span class="text-red-700">*</span>
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
    <span class="block text-sm hover:cursor-pointer my-2 text-blue-700" @click="$emit('phone')">Can I log in with phone number instead?</span>
    <ButtonBar :next="true" @go="next" :required="true"></ButtonBar>
</template>
<script>
import ButtonBar from "./ButtonBar.vue";
import axios from "axios";

export default {
    components: {ButtonBar},
    props: {
        hasErrors: Object,
        prev:Array,
        guardian: {type:Boolean, default: false,},
    },
    emits: ['sendErrors', 'close', 'phone', 'success','back'],
    data() {
        return {
            email: null,
            email_confirm: null,
        }
    },
    methods: {
        sendErrors(value) {
            this.$emit('sendErrors', value);
        },
        next() {
            if (this.email == null) this.sendErrors({email: 'Field is mandatory'});
            if (!this.hasErrors.email) {
                axios.post('/api/users/validate_email',{email:this.email}).then(result => {
                    if (result.data === 0){
                        this.sendErrors({email:"Invalid e-mail address"})
                    }
                     else {this.$emit('success', {email:this.email,data:result.data});}
                }).catch(() => {
                    this.sendErrors({email: "Something went wrong"});
                });
            }
        },
        back(step) {
            this.$emit('back', step);
        },
    }
}
</script>
