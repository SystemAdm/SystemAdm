<template>
    <div class="flex justify-center gap-2">
        <div class="text-gray-500 font-bold flex items-center" @click="$emit('viewQr')">
            <div class="justify-items-center">
                <font-awesome-icon class="size-20" :icon="['fas', 'qrcode']"/>
                <div class="text-sm ml-2">Show my QR code</div>
            </div>
        </div>
        <div class="text-gray-500 font-bold flex items-center" @click="$emit('register')">
            <div class="justify-items-center">
                <font-awesome-icon class="size-20" :icon="['fas', 'id-card']"/>
                <div class="text-sm ml-2">Login or register an account</div>
            </div>
        </div>
    </div>
    <ButtonBar @close="$emit('close')"></ButtonBar>
</template>
<script>
import ButtonBar from "./ButtonBar.vue";

export default {
    components: {ButtonBar},
    emits: ['viewQr', 'register', 'close'],
    data() {
        return {
            option: null,
        }
    }
}
</script>
