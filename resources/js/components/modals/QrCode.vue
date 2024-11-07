<template>
    <div v-if="modalOpen" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full px-4">
        <div class="relative top-40 mx-auto shadow-xl rounded-md bg-white max-w-lg">
            <div class="flex justify-end p-2">
                <button @click="closeModal" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-3xl p-1.5 ml-auto inline-flex items-center">
                    &times;
                </button>
            </div>
            <div class="p-6 pt-0 text-center">
                <h1 class="text-2xl font-extrabold text-black">Sign me up!</h1>

                <button @click="closeModal"
                        class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';

export default {
    components: {FontAwesomeIcon},
    emits: ['closeModal'],
    props: {
        modalOpen: {
            type: Boolean,
            required: true
        },
        selected: {
            type: Number,
            required: false
        }
    },
    data() {
        return {
            qr:null,
            selected: null,
        }
    },
    methods: {
        fetch() {
            axios.post('/api/users/qr', {selected: this.selected}).then(result => {
                this.qr = result.data;
            })
        },
        closeModal() {
            this.$emit('closeModal');
        }
    },
    mounted() {
        this.fetch();
    }
};
</script>
<style scoped>

</style>
