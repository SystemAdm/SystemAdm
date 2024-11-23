<template>
    <div>
        <div class="mb-5">
            <label class="block text-gray-700 text-sm font-bold mb-2">
                Hva ønsker du å gjøre?
            </label>
            <div class="space-y-3">
                <button 
                    @click="selectOption('qr')"
                    class="w-full text-left p-3 border rounded hover:bg-gray-100"
                >
                    Vis min QR-kode
                </button>
                <button 
                    @click="selectOption('create_child')"
                    class="w-full text-left p-3 border rounded hover:bg-gray-100"
                >
                    Registrere barn
                </button>
                <button 
                    @click="selectOption('create_guardian')"
                    class="w-full text-left p-3 border rounded hover:bg-gray-100"
                >
                    Registrere foresatt
                </button>
            </div>
        </div>
        <ButtonBar :prev="prev" @close="$emit('close')" @back="back"></ButtonBar>
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
    emits: ['sendErrors', 'close', 'back', 'success'],
    methods: {
        selectOption(option) {
            let nextStep;
            
            switch(option) {
                case 'qr':
                    nextStep = this.STEPS.QR; // Steg 20
                    break;
                case 'create_child':
                    nextStep = this.STEPS.CHILD_PHONE; // Steg for å registrere barn
                    break;
                case 'create_guardian':
                    nextStep = this.STEPS.GUARDIAN_PHONE; // Steg for å registrere foresatt
                    break;
            }
            
            this.$emit('success', { 
                option: option,
                type: option === 'create_child' ? 'child' : 
                      option === 'create_guardian' ? 'guardian' : 'user'
            }, nextStep);
        },
        back(step) {
            this.$emit('back', step);
        }
    }
}
</script>
