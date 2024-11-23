<template>
    <div>
        <div class="mb-5">
            <label class="block text-gray-700 text-sm font-bold mb-2">
                Din QR-kode
            </label>
            <div class="flex justify-center">
                <div 
                    v-if="user?.id"
                    v-html="qrCode"
                    class="max-w-xs"
                ></div>
                <div v-else class="text-red-600">
                    Bruker-ID mangler
                </div>
            </div>
        </div>
        <ButtonBar :prev="prev" @close="$emit('close')" @back="back"></ButtonBar>
    </div>
</template>

<script>
import axios from 'axios';
import ButtonBar from "../ButtonBar.vue";

export default {
    name: 'QrCode',
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
        user: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            qrCode: null
        }
    },
    async mounted() {
        if (this.user?.id) {
            try {
                const response = await axios.get(`/api/users/${this.user.id}/qr`);
                this.qrCode = response.data;
            } catch (error) {
                console.error('Failed to load QR code:', error);
            }
        }
    },
    emits: ['close', 'back'],
    methods: {
        back(step) {
            this.$emit('back', step);
        }
    }
}
</script>

<style scoped>

</style>
