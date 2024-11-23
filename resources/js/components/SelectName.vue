<template>
    <div>
        <div class="mb-5">
            <label class="block text-gray-700 text-sm font-bold mb-2">
                Velg bruker eller opprett ny
            </label>
            <div v-for="(user, id) in selection" :key="id" class="mb-2">
                <button 
                    @click="selectUser(user)"
                    class="w-full text-left p-3 border rounded hover:bg-gray-100"
                >
                    {{ user.display_name }}
                </button>
            </div>
            <button 
                @click="notMe"
                class="w-full text-left p-3 border rounded hover:bg-gray-100 mt-4 text-blue-600"
            >
                Ikke meg - opprett ny bruker
            </button>
        </div>
        <ButtonBar :prev="prev" @close="$emit('close')" @back="back"></ButtonBar>
    </div>
</template>

<script>
import ButtonBar from "./ButtonBar.vue";

export default {
    components: { ButtonBar },
    props: {
        selection: {
            type: Object,
            required: true
        },
        prev: {
            type: Array,
            required: true
        },
        hasErrors: {
            type: Object,
            required: true
        }
    },
    methods: {
        selectUser(user) {
            // Valgt eksisterende bruker - gå til passordsjekk
            this.$emit('success', {
                selected: user
            }, 4);
        },
        notMe() {
            // "Ikke meg" - gå til NAME (steg 5)
            this.$emit('success', {}, 5);
        },
        back(step) {
            this.$emit('back', step);
        }
    }
}
</script>
