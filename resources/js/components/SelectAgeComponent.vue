<template>
    <div>
        <div class="mb-5">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="birthdate">
                Fødselsdato <span class="text-red-700">*</span>
            </label>
            <input
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                id="birthdate"
                type="date"
                v-model="birthdate"
                required
                :class="{'border-red-700': hasErrors.birthdate}"
                @focus="sendErrors({birthdate: false})"
                @change="calculateAge">
            <span class="ml-3 mt-2 block text-red-700" v-if="hasErrors.birthdate">{{ hasErrors.birthdate }}</span>

            <!-- Vis aldersgrenser når fødselsdato er valgt -->
            <div v-if="calculatedAge" class="mt-5">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Din aldersgrense
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <!-- PEGI -->
                    <div>
                        <h3 class="text-sm font-semibold mb-2">PEGI</h3>
                        <div class="flex justify-center">
                            <img 
                                :src="`/images/pegi/age-${currentPegiAge}.jpg`" 
                                :alt="`PEGI ${currentPegiAge}`"
                                class="h-24 w-24 object-contain"
                            >
                        </div>
                    </div>

                    <!-- ESRB -->
                    <div>
                        <h3 class="text-sm font-semibold mb-2">ESRB</h3>
                        <div class="flex justify-center">
                            <img 
                                :src="`/images/esrb/${currentEsrbRating.image}.svg`" 
                                :alt="`ESRB ${currentEsrbRating.label}`"
                                class="h-24 w-24 object-contain"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ButtonBar 
            :prev="prev" 
            :next="true"
            @close="$emit('close')" 
            @go="validateAndContinue" 
            @back="back"
        ></ButtonBar>
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
        }
    },
    data() {
        return {
            birthdate: null,
            calculatedAge: null,
            pegiAges: [3, 7, 12, 16, 18],
            esrbRatings: [
                { age: 3, label: 'Early Childhood', image: 'EC' },
                { age: 6, label: 'Everyone', image: 'E' },
                { age: 10, label: 'Everyone 10+', image: 'E10plus' },
                { age: 13, label: 'Teen', image: 'T' },
                { age: 17, label: 'Mature', image: 'M' },
                { age: 18, label: 'Adults Only', image: 'AO' }
            ]
        }
    },
    computed: {
        currentPegiAge() {
            if (!this.calculatedAge) return null;
            return this.pegiAges.reduce((prev, curr) => {
                return (curr <= this.calculatedAge && curr > prev) ? curr : prev;
            }, 0);
        },
        currentEsrbRating() {
            if (!this.calculatedAge) return null;
            return this.esrbRatings.reduce((prev, curr) => {
                return (curr.age <= this.calculatedAge && curr.age > prev.age) ? curr : prev;
            }, this.esrbRatings[0]);
        }
    },
    methods: {
        sendErrors(value) {
            this.$emit('sendErrors', value);
        },
        calculateAge() {
            if (!this.birthdate) return;
            
            const today = new Date();
            const birthDate = new Date(this.birthdate);
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            
            this.calculatedAge = age;
        },
        validateAndContinue() {
            if (!this.birthdate) {
                this.sendErrors({birthdate: 'Fødselsdato må fylles ut'});
                return;
            }
            
            if (!this.calculatedAge) {
                this.sendErrors({birthdate: 'Ugyldig fødselsdato'});
                return;
            }

            this.$emit('success', { 
                birthdate: this.birthdate,
                age: this.calculatedAge 
            }, 7);
        },
        back(step) {
            this.$emit('back', step);
        }
    }
}
</script>

<style scoped>
img {
    max-width: 100%;
    max-height: 100%;
}
</style>
