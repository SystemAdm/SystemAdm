<template>
    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="firstname">Firstname <span
            class="text-red-700">*</span></label>
        <!-- INPUT NAME FIRST -->
        <input
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            id="firstname" type="text" placeholder="Firstname" v-model="firstname" :class="{'border-red-700':hasErrors.firstname}" v-on:focus="errors({firstname: false})">
        <span class="mb-5 block text-red-700" v-if="hasErrors.firstname">{{ hasErrors.firstname }}</span>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="middlename">Middlename <span
            class="text-gray-400">(optional)</span></label>
        <!-- INPUT NAME MIDDLE -->
        <input
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            id="middlename" type="text" placeholder="Middlename" v-model="middlename" :class="{'border-red-700':hasErrors.middlename}" v-on:focus="errors({middlename: false})">
        <span class="mb-5 block text-red-700" v-if="hasErrors.middlename">{{ hasErrors.middlename }}</span>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="lastname">Lastname <span
            class="text-red-700">*</span></label>
        <!-- INPUT NAME LAST -->
        <input
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            id="lastname" type="text" placeholder="Lastname" v-model="lastname" :class="{'border-red-700':hasErrors.lastname}" v-on:focus="errors({lastname: false})">
        <span class="mb-5 block text-red-700" v-if="hasErrors.lastname">{{ hasErrors.lastname }}</span>
    </div>
    <ButtonBar :next="true" :required="true" @go="go" @close="$emit('close')"></ButtonBar>
</template>
<script>
import ButtonBar from "./ButtonBar.vue";
import axios from "axios";

export default {
    components: {ButtonBar},
    emits: ['user', 'sendErrors','close'],
    props: {
        hasErrors: {
            type: Object,
        },
        phone:{
            type:String,
            required: true,
        }
    },
    data() {
        return {
            firstname: null,
            middlename: null,
            lastname: null,
        }
    },
    methods: {
        errors(value) {
            this.$emit('sendErrors', value)
        },
        go() {
            if (this.firstname == null || this.firstname === '') {
                this.errors({firstname: 'Firstname is required'});
            } else if (this.firstname.length < 2) {
                this.errors({firstname: 'Firstname must contain more than 2 characters'});
            }
            if (this.lastname == null || this.lastname === '') {
                this.errors({lastname: 'Lastname is required'});
            } else if (this.lastname.length < 2) {
                this.errors({lastname: 'Lastname must contain more than 2 characters'});
            }
            if (this.middlename != null && this.middlename !== '') {
                if (this.middlename.length < 2) {
                    this.errors({middlename: 'Middlename must contain more than 2 characters'});
                }
            }
            if (!this.hasErrors.firstname || !this.hasErrors.firstname || !this.hasErrors.middlename) {
                axios.post('/api/users',{firstname:this.firstname,middlename:this.middlename,lastname:this.lastname}).then(response => {
                    this.$emit('user',response.data);
                }).catch(error => {
                    this.$emit('sendErrors', error.response.data.errors);
                });
                /*this.$emit('user', {
                    first_name: this.firstname,
                    middle_name: this.middlename,
                    last_name: this.lastname
                });*/
            }
        }
    }
}
</script>
