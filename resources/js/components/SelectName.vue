<template>
    <label for="name">From your phone number, we found a registration on our site! We need a confirmation from you.<br/><span class="font-bold" :class="{'text-red-500 text-xl':hasErrors.select_name}">Are you among us?:</span></label>
    <div class="flex justify-center" v-for="name in selection" :key="name.id">
        <div class="text-gray-500 font-bold flex items-center" @click="selectedName = name.id">
            <input class="hidden" type="radio" id="name" v-model="selectedName" :value="name.id" required>
            <div @click="error({select_name:false})" :class="['border-4 p-1 transition-all duration-300',selectedName === name.id ? 'border-blue-500' : 'border-transparent']" class="justify-items-center">
                <img :src="name.profile.image" alt="User Avatar" class="w-12 h-12 rounded-full object-cover">
                <span class="text-sm ml-2"><font-awesome-icon class="text-orange-500" v-if="name.active" :icon="['fas','lock']" />&nbsp;{{ name.first_name }}</span>
            </div>
        </div>
    </div>
    <div class="justify-self-start mb-5">
        <label class="block text-black font-bold">
            <input @select="error({select_name:false})" class="mr-2 leading-tight" type="radio" name="restriction" id="attending_crew" v-model="selectedName" :value="0">
            <span class="text-xl">Nah. I am someone else</span>
        </label>
    </div>
    <ButtonBar :next="true" @go="go" @close="$emit('close')"></ButtonBar>
</template>
<script>
import ButtonBar from "./ButtonBar.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

export default {
    components: {FontAwesomeIcon, ButtonBar},
    emits:['selectUser','sendErrors','close'],
    props:{
        selection:{
            type:Object,
        },
        hasErrors:{
            type:Object,
        }
    },
    data() {
        return {
            selectedName: null,
        }
    },
    methods:{
        go(){
            if(this.selectedName === null){
                this.error({select_name:true});
            } else {
                this.$emit('selectUser', (this.selectedName !== 0)?this.selection[this.selectedName]:0);
            }
        }, error(value) {
            this.$emit('sendErrors',value);
        }
    },
    mounted() {
        console.log(this.selection)
    }
}
</script>
