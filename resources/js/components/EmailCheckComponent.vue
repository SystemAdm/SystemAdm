<template>
    <div>
        <div class="mb-5">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                E-post <span class="text-red-700">*</span>
            </label>
            <input
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                id="email"
                type="email"
                placeholder="E-post"
                v-model="email"
                required
                :class="{'border-red-700': hasErrors.email}"
                @focus="sendErrors({email: false})">
            <span class="ml-3 mt-2 block text-red-700" v-if="hasErrors.email">{{ hasErrors.email }}</span>
        </div>
        <ButtonBar :prev="prev" :next="true" @close="$emit('close')" @go="validateEmail" @back="back"></ButtonBar>
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
            email: null,
            errors: {}
        }
    },
    methods: {
        sendErrors(value) {
            this.$emit('sendErrors', value);
        },
        async validateEmail() {
            if (!this.email) {
                this.sendErrors({email: 'E-post må fylles ut'});
                return;
            }

            try {
                const response = await axios.post('/api/users/validate_email', {
                    email: this.email
                });
                
                console.log('Email validation response:', response.data);

                if (response.data === 0) {
                    this.sendErrors({email: 'Ugyldig e-postadresse'});
                    return;
                }
                
                if (response.data === 1) {
                    // Gyldig e-post, men ikke i databasen - gå til NAME (steg 5)
                    this.$emit('success', {
                        email: this.email
                    }, 5);
                    return;
                }
                
                if (typeof response.data === 'object') {
                    // E-post finnes i databasen - gå til SELECT_NAME (steg 3)
                    this.$emit('success', {
                        email: this.email,
                        selection: response.data
                    }, 3);
                    return;
                }

            } catch (error) {
                console.error('Email validation error:', error);
                this.sendErrors({email: 'En feil oppstod under validering'});
            }
        },
        back(step) {
            this.$emit('back', step);
        }
    }
}
</script>
