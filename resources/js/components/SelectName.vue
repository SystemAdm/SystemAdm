<template>
    <div>
        <h2 v-if="guardian" class="text-3xl">{{ title }}</h2>
        
        <label for="name" class="block">
            {{ t('auth.found_registration') }}<br/>
            <span class="font-bold" :class="{'text-red-500 text-xl': hasErrors.select_name}">
                {{ t('auth.select_user') }}
            </span>
        </label>

        <!-- Brukervalg -->
        <div class="flex justify-center" v-for="name in selection" :key="name.id">
            <div class="text-gray-500 font-bold flex" @click="selectName(name.id)">
                <input 
                    type="radio" 
                    class="hidden" 
                    id="name" 
                    v-model="selected_name" 
                    :value="name.id" 
                    required
                >
                <div 
                    class="border-4 p-1 transition-all duration-300 justify-center"
                    :class="[isSelected(name) ? 'border-blue-500' : 'border-transparent']"
                >
                    <img 
                        :src="name.profile.image" 
                        :alt="t('auth.user_avatar')" 
                        class="w-12 h-12 rounded-full object-cover m-auto"
                    >
                    <span class="text-sm">
                        <font-awesome-icon 
                            v-if="name.active" 
                            class="text-orange-500" 
                            :icon="['fas','lock']"
                        />&nbsp;{{ name.given_name }}
                    </span>
                </div>
            </div>
        </div>

        <!-- "Noen andre" valget -->
        <div class="justify-self-start mb-5">
            <label class="text-black font-bold">
                <input 
                    @select="sendErrors({select_name:false})" 
                    class="mr-2 leading-tight" 
                    type="radio" 
                    name="restriction"
                    id="attending_crew" 
                    v-model="selected_name" 
                    :value="0"
                >
                <span class="text-xl">{{ t('auth.someone_else') }}</span>
            </label>
        </div>

        <ButtonBar 
            :next="true" 
            :prev="prev" 
            @back="back" 
            @go="go" 
            @close="$emit('close')"
        />
    </div>
</template>
<script>
import ButtonBar from "./ButtonBar.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

export default {
    name: 'SelectName',
    components: { 
        FontAwesomeIcon, 
        ButtonBar 
    },
    
    setup() {
        const { t } = useI18n();
        return { t };
    },
    
    emits: ['success', 'sendErrors', 'close', 'back'],
    props: {
        prev: Array,
        selection: Object,
        hasErrors: Object,
        selected: Object,
        guardian: {type:Boolean, default: false,},
    },
    data() {
        return {
            selected_name: null,
        }
    },
    methods: {
        go() {
            if (this.selected_name === null) {
                this.sendErrors({select_error: true});
            } else {
                this.$emit('success', (this.selected_name !== 0) ? this.selection[this.selected_name] : 0);
            }
        },
        sendErrors(value) {
            this.$emit('sendErrors', value);
        },
        back(step) {
            this.$emit('back', step);
        }
    },
    computed: {
        title() {
            return this.guardian ? this.t('auth.guardian_parent') : this.t('auth.among_us');
        }
    }
}
</script>
