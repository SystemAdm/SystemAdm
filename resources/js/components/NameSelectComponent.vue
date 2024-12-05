<template v-if="profiles.length > 0">
    <div
        v-for="user in profiles"
        :key="user.id"
        @click="selectProfile(user)"
        class="p-4 border rounded-lg hover:bg-gray-50 cursor-pointer flex items-center justify-between"
    >
        <span class="flex items-center">
            <font-awesome-icon
                :icon="['fas', 'user']"
                class="mr-3 text-gray-600"
            />
            {{ user.full_name }}
        </span>

        <font-awesome-icon
            v-if="user.active"
            :icon="['fas', 'lock']"
            class="text-gray-500"
            :title="trans('auth.password_required')"
        />
    </div>
</template>
<script setup>
import {trans} from "laravel-vue-i18n";

const props = defineProps({
    profiles: {
        type: Array,
        required: true
    }
});

const emit = defineEmits(['select']);

const selectProfile = (user) => {
    emit('select', user);
}
</script>
