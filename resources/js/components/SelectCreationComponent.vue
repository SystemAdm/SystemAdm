<template>
    <div class="flex justify-center columns-3">
        <div class="font-bold flex items-center w-1/3 px-2" @click="$emit('viewQr')">
            <div class="justify-items-center text-black">
                <font-awesome-icon class="size-20" :icon="['fas', 'qrcode']"/>
                <div class="text-sm ">Show my QR code</div>
            </div>
        </div>
        <div class="font-bold flex items-center w-1/3 px-2" @click="$emit('register')">
            <div class="justify-items-center text-black">
                <font-awesome-icon class="size-20" :icon="['fas', 'id-card']"/>
                <div class="text-sm ">Guest/User account</div>
            </div>
        </div>
        <div class="font-bold flex items-center w-1/3 px-2" v-if="age >= 18" @click="$emit('guardian')">
            <div class="justify-items-center text-black">
                <font-awesome-icon class="size-20" :icon="['fas', 'people-pulling']"/>
                <div class="text-sm ">Parent/Guardian account</div>
            </div>
        </div>
    </div>
    <ButtonBar :prev="prev" @back="back" @close="$emit('close')"></ButtonBar>
</template>
<script>
import ButtonBar from "./ButtonBar.vue";

export default {
    props:{
        age:Number,
        prev:Array,
    },
    components: {ButtonBar},
    emits: ['viewQr', 'register', 'close', 'guardian','back'],
    data() {
        return {
            option: null,
        }
    },
    methods: {
        back(step) {
            this.$emit('back', step);
        }
    }
}
</script>
