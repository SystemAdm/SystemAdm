<script setup>
import { ref } from 'vue';

const props = defineProps({
    items: {
        type: Array,
        required: true
    },
    type: {
        type: String,
        required: true,
        validator: (value) => ['email', 'phone'].includes(value)
    },
    editable: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:items']);

const newItem = ref('');

const addItem = () => {
    if (newItem.value.trim()) {
        const updatedItems = [...props.items, {
            value: newItem.value.trim(),
            primary: props.items.length === 0, // Første item blir primary
            verified: false
        }];
        emit('update:items', updatedItems);
        newItem.value = '';
    }
};

const removeItem = (index) => {
    const updatedItems = [...props.items];
    updatedItems.splice(index, 1);
    
    // Hvis vi fjernet primary, sett første som primary
    if (updatedItems.length > 0 && !updatedItems.some(item => item.primary)) {
        updatedItems[0].primary = true;
    }
    
    emit('update:items', updatedItems);
};

const setPrimary = (index) => {
    const updatedItems = props.items.map((item, i) => ({
        ...item,
        primary: i === index
    }));
    emit('update:items', updatedItems);
};
</script>

<template>
    <div class="space-y-3 text-black">
        <div v-for="(item, index) in items" :key="index" 
             class="flex items-center gap-2 p-2 bg-gray-50 rounded">
            <div class="flex-grow">
                <span :class="{ 'font-bold': item.primary }">{{ item.value }}</span>
                <span v-if="item.primary" 
                      class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded">
                    {{ $t('profile.primary') }}
                </span>
                <span v-if="item.verified" 
                      class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">
                    {{ $t('profile.verified') }}
                </span>
            </div>
            
            <div v-if="editable" class="flex items-center gap-2">
                <button v-if="!item.primary"
                        @click="setPrimary(index)"
                        type="button"
                        class="text-sm text-blue-600 hover:text-blue-800">
                    {{ $t('profile.make_primary') }}
                </button>
                <button @click="removeItem(index)"
                        type="button"
                        class="text-sm text-red-600 hover:text-red-800">
                    {{ $t('common.remove') }}
                </button>
            </div>
        </div>

        <div v-if="editable" class="flex gap-2">
            <input 
                :type="type"
                v-model="newItem"
                :placeholder="$t(`profile.add_${type}`)"
                class="flex-grow rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                @keyup.enter="addItem"
            >
            <button 
                @click="addItem"
                type="button"
                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                {{ $t('common.add') }}
            </button>
        </div>
    </div>
</template> 