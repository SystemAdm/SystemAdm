<template>
    <div class="p-6 pt-0 text-center items-center">
        <img :src="image" alt="" class="m-auto py-5">
    </div>
    <ButtonBar @close="$emit('close')"></ButtonBar>
</template>

<script>
import axios from "axios";
import ButtonBar from "../ButtonBar.vue";

export default {
    emits:['close'],
    props:{
        selectedUser:{
            type:Object,
        }
    },
    components: {ButtonBar},
    data() {
        return {
            qr: null,
            image: '',
        }
    },
    methods: {
        fetch() {
            axios.post('/api/users/qr', {selected: this.selectedUser.id}).then(result => {
                this.qr = result.data;
                this.image = `data:image/png;base64,${this.qr}`;
            })
        },
    },
    mounted() {
        this.fetch();
    }
};
</script>
<style scoped>

</style>
