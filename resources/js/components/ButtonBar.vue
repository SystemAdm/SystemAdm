<template>
    <div v-show="required" class="block text-black m-3 text-sm">
        {{ $t('common.required_fields') }} <span class="text-red-700">*</span>
    </div>
    <div class="flex flex-wrap justify-center gap-2">
        <button
            v-for="(btn, key) in buttons"
            :key="key"
            v-show="btn.show"
            type="button"
            :class="[
                btn.class,
                disabled && (key === 'next' || key === 'submit') ? 'opacity-50 cursor-not-allowed' : ''
            ]"
            :disabled="disabled && (key === 'next' || key === 'submit')"
            @click="btn.action"
        >
            <font-awesome-icon 
                v-if="btn.icon && btn.iconPosition === 'left'" 
                :icon="btn.icon" 
                :class="btn.iconClass"
            />
            {{ $t(btn.text) }}
            <font-awesome-icon 
                v-if="btn.icon && btn.iconPosition === 'right'" 
                :icon="btn.icon" 
                :class="btn.iconClass"
            />
        </button>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const props = defineProps({
    next: Boolean,
    prev: {
        type: Array,
        default: () => []
    },
    currentStep: {
        type: Number,
        default: 1
    },
    required: Boolean,
    submit: Boolean,
    canDelete: Boolean,
    trashed: Boolean,
    loading: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['go', 'close', 'back', 'delete', 'force', 'restore', 'reset']);

const baseButtonClass = `
    py-2 px-3 text-white font-medium rounded-lg text-base 
    inline-flex items-center text-center focus:ring-4
`;

const handleReset = () => {
    if (props.currentStep === 1 || props.currentStep === 2) {
        router.push({ name: 'Index' });
    } else {
        emit('reset');
        emit('back', 1);
    }
};

const handleClose = () => {
    if (props.currentStep === 1 || props.currentStep === 2) {
        router.push({ name: 'Index' });
    }
};

const buttons = computed(() => ({
    back: {
        show: props.prev?.length > 0,
        icon: ['fas', 'arrow-left'],
        iconClass: 'mr-2',
        iconPosition: 'left',
        text: 'common.back',
        class: `${baseButtonClass} bg-gray-600 hover:bg-gray-800 focus:ring-gray-300`,
        action: () => {
            emit('back', props.prev[props.prev.length - 1]);
        }
    },
    startOver: {
        show: props.currentStep >= 3,
        icon: ['fas', 'rotate-left'],
        iconClass: 'mr-2',
        iconPosition: 'left',
        text: 'common.start_over',
        class: `${baseButtonClass} bg-red-600 hover:bg-red-800 focus:ring-red-300`,
        action: handleReset
    },
    close: {
        show: props.prev?.length === 0,
        icon: ['fas', 'xmark'],
        iconClass: 'mr-2',
        iconPosition: 'left',
        text: 'common.close',
        class: `${baseButtonClass} bg-red-600 hover:bg-red-800 focus:ring-red-300`,
        action: handleClose
    },
    next: {
        show: props.next && !props.trashed,
        icon: ['fas', 'arrow-right'],
        iconClass: 'ml-2',
        iconPosition: 'right',
        text: 'common.next',
        class: `${baseButtonClass} bg-blue-600 hover:bg-blue-800 focus:ring-blue-300`,
        action: () => emit('go')
    },
    submit: {
        show: props.submit && !props.trashed,
        icon: ['fas', 'arrow-right'],
        iconClass: 'ml-2',
        iconPosition: 'right',
        text: 'common.submit',
        class: `${baseButtonClass} bg-green-600 hover:bg-green-800 focus:ring-green-300`,
        action: () => emit('go')
    },
    delete: {
        show: props.canDelete && !props.trashed,
        icon: ['fas', 'trash-can'],
        iconClass: 'pr-2',
        text: 'common.delete',
        class: `${baseButtonClass} bg-red-600 hover:bg-red-700 focus:ring-red-300`,
        action: () => emit('delete')
    },
    forceDelete: {
        show: props.trashed,
        icon: ['fas', 'trash-can'],
        iconClass: 'pr-2',
        text: 'common.force_delete',
        class: `${baseButtonClass} bg-red-600 hover:bg-red-700 focus:ring-red-300`,
        action: () => emit('force')
    },
    restore: {
        show: props.trashed,
        icon: ['fas', 'trash-can-arrow-up'],
        iconClass: 'pr-2',
        text: 'common.restore',
        class: `${baseButtonClass} bg-orange-600 hover:bg-orange-700 focus:ring-orange-300`,
        action: () => emit('restore')
    }
}));
</script>
