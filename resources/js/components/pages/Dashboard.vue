<template>
    <div class="px-4 w-full mx-auto" v-if="me !== null">
        <div class="items-center mb-8">
            <div class="grid grid-cols-12 gap-6">
                <div
                    class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-gray-800 shadow-sm rounded-xl p-4 mb-4">
                    <div class="px-3 pt-3">
                        <div class="flex justify-between items-start mb-2">
                            <h2 class="text-lg font-bold text-gray-100 mb-2">
                                {{ trans('common.phone') }}:
                            </h2>
                            <div class="relative inline-flex"><span class="text-green-500"><FontAwesomeIcon
                                :icon="['fas','plus']"/> {{ trans('common.add_new') }}</span></div>
                        </div>
                        <div v-for="phone in me.phones" class="flex justify-between items-start mb-2">
                            <div v-if="phone !== null">
                                <span v-if="phone.verified_by !== null" class="text-blue-500 mr-2">
                                    <abbr :title="'Verified @'+phone.verified_at">
                                    <FontAwesome-icon :icon="['fas', 'stamp']"/></abbr>
                                </span>
                                <span v-if="phone.pivot.verified_by !== null" class="text-green-500 mr-2">
                                    <abbr :title="'Verified @'+phone.pivot.verified_at">
                                    <FontAwesome-icon :icon="['fas', 'link']"/></abbr>
                                </span>
                                <span v-if="phone.pivot.primary === 1" class="text-yellow-500 mr-2">
                                    <abbr :title="trans('common.primary')"><FontAwesome-icon
                                        :icon="['fas', 'star']"/></abbr>
                                </span>
                                <button @click="handleSetPrimaryPhone(phone.id)" v-else class="text-gray-500 mouse">
                                    <abbr :title="trans('common.set_primary')"><FontAwesomeIcon
                                        :icon="['far','star']"/>{{ trans('common.set_primary') }}</abbr>
                                </button>
                            </div>
                            <span>{{ phone.full_phone }}</span>
                            <div>

                                <span class="text-orange-500 ml-2"><abbr :title="trans('common.edit')"><FontAwesomeIcon
                                    :icon="['fas','pencil']"/>{{ trans('common.edit') }}</abbr></span>
                                <span class="text-red-500 ml-2"><abbr :title="trans('common.delete')"><FontAwesomeIcon
                                    :icon="['fas','trash']"/>{{ trans('common.delete') }}</abbr></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-gray-800 shadow-sm rounded-xl p-4 mb-4">
                    <div class="px-5 pt-5">
                        <div class="flex justify-between items-start mb-2">
                            <h2 class="text-lg font-bold text-gray-100 mb-2">
                                {{ trans('common.email') }}:
                            </h2>
                            <div class="relative inline-flex"><span class="text-green-500"><FontAwesomeIcon
                                :icon="['fas','plus']"></FontAwesomeIcon> {{ trans('common.add_new') }}</span></div>
                        </div>
                        <div v-for="email in me.emails" class="flex justify-between items-start mb-2">
                            <span>{{ email.email }}</span>
                            <div>
                                <span class="text-orange-500 mx-2"><FontAwesomeIcon
                                    :icon="['fas','pencil']"></FontAwesomeIcon></span>
                                <span class="text-red-500 mx-2"><FontAwesomeIcon
                                    :icon="['fas','trash']"></FontAwesomeIcon></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-gray-800 shadow-sm rounded-xl p-4 mb-4">
                    <div class="px-5 pt-5">
                        <div class="flex justify-between items-start mb-2">
                            <h2 class="text-lg font-bold text-gray-100 mb-2">
                                Her bor jeg:
                            </h2>
                            <div class="relative inline-flex">
                                <span class="text-orange-500 mx-2"><FontAw2someI1on
                                    :icon="['fas','pencil']" class="ml-2"></FontAw2someI1on> Jeg har flyttet</span>
                            </div>
                        </div>
                        <div v-if="me.profile.postal == null || me.profile.show_address === 0">--HIDDEN--</div>
                        <div :class="{'text-gray-500':me.profile.show_address <= 0}">Land: <span>{{ me.profile.postal.country }}</span></div>
                        <div :class="{'text-gray-500':me.profile.show_address <= 1}">Fylke: <span>{{ me.profile.postal.county }}</span></div>
                        <div :class="{'text-gray-500':me.profile.show_address <= 2}">Kommune: <span>{{ me.profile.postal.municipality }}</span></div>
                        <div :class="{'text-gray-500':me.profile.show_address <= 3}">Poststed: <span>{{ me.profile.postal.name }}</span></div>
                        <div :class="{'text-gray-500':me.profile.show_address <= 4}">Postnummer: <span>{{ me.profile.postal_code }}</span></div>
                        <div :class="{'text-gray-500':me.profile.show_address <= 5}">Gateadresse: <span>{{ me.profile.address }}</span></div>

                    </div>
                </div>
                <div
                    class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-gray-800 shadow-sm rounded-xl p-4 mb-4">
                    <div class="px-5 pt-5">
                        <div class="flex justify-between items-start mb-2">
                            <h2 class="text-lg font-bold text-gray-100 mb-2">
                                Events
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {trans} from "laravel-vue-i18n";
import {onMounted} from "vue";
import axios from "axios";

const props = defineProps({
    me: {
        type: Object,
    }
});

const emits = defineEmits(['update']);

const handleSetPrimaryPhone = (phone) => {
    axios.post('/api/phones/' + phone + '/setPrimary', {user: props.me.id}).then(response => {
        emits('update');
    })
}

</script>
<style scoped>
abbr {
    text-decoration: none;
}
</style>
