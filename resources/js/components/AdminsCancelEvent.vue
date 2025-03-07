<template>
    <div class="text-gray-800 dark:text-gray-200">
                <!-- Modal for avlysning -->
        <div v-if="showCancelModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-semibold mb-4">{{ trans('events.CancelEvent') }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    {{ trans('events.CancelReasonPrompt') }}
                </p>

                <!-- Begrunnelse -->
                <textarea
                    v-model="cancelReason"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white p-2 mb-4"
                    rows="3"
                    :placeholder="trans('events.CancelReasonPlaceholder')"
                />

                <!-- Valg med checkbokser -->
                <div class="flex flex-col space-y-2 mb-4">
                    <label class="flex items-center space-x-2">
                        <input
                            type="checkbox"
                            v-model="notifyCrew"
                            class="rounded border-gray-300 dark:border-gray-700"
                        />
                        <span>{{ trans('events.NotifyCrew') }}</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input
                            type="checkbox"
                            v-model="notifyAttendees"
                            class="rounded border-gray-300 dark:border-gray-700"
                        />
                        <span>{{ trans('events.NotifyAttendees') }}</span>
                    </label>
                </div>

                <!-- Handling-knapper -->
                <div class="flex justify-end space-x-2">
                    <button
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300"
                        @click="closeCancelModal"
                    >
                        {{ trans('Cancel') }}
                    </button>
                    <button
                        class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded hover:bg-red-600"
                        @click="submitCancelEvent"
                    >
                        {{ trans('events.ConfirmCancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import {ref} from "vue";
import {trans} from 'laravel-vue-i18n';
import axios from "axios";

const notifyCrew = ref(false);
const notifyAttendees = ref(false);
const cancelReason = ref('');

const props = defineProps({
    showCancelModal: Boolean,
    id: String,
});

const emits = defineEmits(['update:showCancelModal']);

function closeCancelModal() {
    cancelReason.value = '';
    emits('update:showCancelModal', false);
}
async function submitCancelEvent() {
    axios.post(`/api/admin/events/${props.id}/cancel`, {
        notify_crew: notifyCrew.value,
        notify_guests: notifyAttendees.value,
        cancelled_text: cancelReason.value,
    }).then(response => {
        console.log(response);
        if (response.data.success) {
            emits('update:showCancelModal', false);
        } else {
            console.log(response.data.errors);
        }
    })
}
</script>
