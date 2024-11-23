<template>
    <div class="mb-5">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
            Passord <span class="text-red-700">*</span>
            <span class="block text-gray-400 text-sm">Denne kontoen er beskyttet med passord</span>
        </label>
        <input
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            id="password"
            type="password"
            placeholder="Passord"
            v-model="password"
            required
            :class="{'border-red-700': hasErrors.password}"
            @focus="sendErrors({password: false})">
        <span class="ml-3 mt-2 block text-red-700" v-if="hasErrors.password">{{ hasErrors.password }}</span>
    </div>
    <ButtonBar :prev="prev" :next="true" @close="$emit('close')" @go="check" @back="back"></ButtonBar>
</template>

<script>
import ButtonBar from "./ButtonBar.vue";
import axios from "axios";
export default {
    components: {ButtonBar},
    props: {
        prev: {
            type: Array,
            required: true
        },
        hasErrors: {
            type: Object,
            required: true
        },
        selected: {
            type: Object,
            required: true
        },
        STEPS: {
            type: Object,
            required: true
        }
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
                this.sendErrors({password: 'Passord må fylles ut'});
                return;
            }
            
            console.log('Checking password for user:', this.selected);
            
            axios.post('/api/users/check', {
                p: this.password, 
                u: this.selected.id
            })
            .then(response => {
                console.log('Password check response:', response.data);
                if (response.data === true) {
                    this.$emit('success', { 
                        password: this.password,
                        selected: this.selected 
                    }, 11); // Endret fra 19 til 11 (CREATION_SELECT)
                } else {
                    this.sendErrors({password: 'Feil passord'});
                }
            })
            .catch(error => {
                console.error('Password check error:', error);
                this.sendErrors({password: 'En feil oppstod under passordsjekk'});
            });
        },
        back(step) {
            this.$emit('back', step);
        }
    }
}
</script>
