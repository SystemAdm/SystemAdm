<template>
    <div>
        <div class="mb-5">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="given_name">
                Fornavn <span class="text-red-700">*</span>
            </label>
            <input
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                id="given_name"
                type="text"
                placeholder="Fornavn"
                v-model="given_name"
                required
                :class="{'border-red-700': hasErrors.given_name}"
                @focus="sendErrors({given_name: false})">
            <span class="ml-3 mt-2 block text-red-700" v-if="hasErrors.given_name">{{ hasErrors.given_name }}</span>
        </div>

        <div class="mb-5">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="additional_name">
                Mellomnavn
            </label>
            <input
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                id="additional_name"
                type="text"
                placeholder="Mellomnavn"
                v-model="additional_name"
                :class="{'border-red-700': hasErrors.additional_name}"
                @focus="sendErrors({additional_name: false})">
            <span class="ml-3 mt-2 block text-red-700" v-if="hasErrors.additional_name">{{ hasErrors.additional_name }}</span>
        </div>

        <div class="mb-5">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="family_name">
                Etternavn <span class="text-red-700">*</span>
            </label>
            <input
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                id="family_name"
                type="text"
                placeholder="Etternavn"
                v-model="family_name"
                required
                :class="{'border-red-700': hasErrors.family_name}"
                @focus="sendErrors({family_name: false})">
            <span class="ml-3 mt-2 block text-red-700" v-if="hasErrors.family_name">{{ hasErrors.family_name }}</span>
        </div>

        <ButtonBar :prev="prev" :next="true" @close="$emit('close')" @go="validateName" @back="back"></ButtonBar>
    </div>
</template>

<script>
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
            given_name: null,
            additional_name: null,
            family_name: null
        }
    },
    methods: {
        sendErrors(value) {
            this.$emit('sendErrors', value);
        },
        validateName() {
            if (!this.given_name) {
                this.sendErrors({given_name: 'Fornavn må fylles ut'});
                return;
            }
            if (!this.family_name) {
                this.sendErrors({family_name: 'Etternavn må fylles ut'});
                return;
            }

            this.$emit('success', {
                given_name: this.given_name,
                additional_name: this.additional_name,
                family_name: this.family_name
            }, 6); // Gå til neste steg (SELECT_AGE)
        },
        back(step) {
            this.$emit('back', step);
        }
    }
}
</script>
