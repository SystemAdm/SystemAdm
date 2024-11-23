<template>
    <div>
        <div class="mb-5">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                Telefonnummer <span class="text-red-700">*</span>
            </label>
            <input
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                id="phone"
                type="tel"
                placeholder="Telefonnummer"
                v-model="phone"
                required
                :class="{'border-red-700': hasErrors.phone}"
                @focus="sendErrors({phone: false})">
            <span class="ml-3 mt-2 block text-red-700" v-if="hasErrors.phone">{{ hasErrors.phone }}</span>
            
            <!-- Bare én knapp for å gå til e-post -->
            <button 
                @click="skipPhone"
                class="mt-4 text-sm text-blue-600 hover:text-blue-800"
            >
                Jeg har ikke telefonnummer
            </button>
        </div>
        <ButtonBar :prev="prev" :next="true" @close="$emit('close')" @go="validatePhone" @back="back"></ButtonBar>
    </div>
</template>

<script>
import axios from 'axios';
import ButtonBar from "./ButtonBar.vue";

export default {
    components: { ButtonBar },
    props: {
        prev: {
            type: Array,
            required: true
        },
        hasErrors: {
            type: Object,
            required: true
        },
        STEPS: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            phone: null,
            errors: {}
        }
    },
    methods: {
        sendErrors(value) {
            this.$emit('sendErrors', value);
        },
        skipPhone() {
            // Gå direkte til e-post-steget
            this.$emit('success', {
                phone: null
            }, 2);
        },
        async validatePhone() {
            if (!this.phone) {
                this.sendErrors({phone: 'Telefonnummer må fylles ut'});
                return;
            }

            try {
                const response = await axios.post('/api/users/validate_phone', {
                    phone: this.phone
                });
                
                console.log('Phone validation response:', response.data);

                if (response.data === 0) {
                    this.sendErrors({phone: 'Ugyldig telefonnummer'});
                    return;
                }
                
                if (response.data === 1) {
                    this.$emit('success', {
                        phone: this.phone
                    }, 5);
                    return;
                }
                
                if (typeof response.data === 'object') {
                    this.$emit('success', {
                        phone: this.phone,
                        selection: response.data
                    }, 3);
                    return;
                }

            } catch (error) {
                console.error('Phone validation error:', error);
                this.sendErrors({phone: 'En feil oppstod under validering'});
            }
        },
        back(step) {
            this.$emit('back', step);
        }
    }
}
</script>
