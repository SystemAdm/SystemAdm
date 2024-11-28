<script setup>
import { ref } from 'vue';
import { notify } from './../utils/notify';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    profile: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['update:profile', 'close']);

// Kopier profildata til form
const form = ref({
    given_name: props.profile.given_name || '',
    family_name: props.profile.family_name || '',
    additional_name: props.profile.additional_name || '',
    phones: props.profile.phones?.map(phone => ({
        id: phone.id,
        number: phone.number,
        type: phone.type,
        is_primary: phone.pivot.is_primary,
        _delete: false  // For å markere sletting
    })) || [],
    emails: props.profile.emails?.map(email => ({
        id: email.id,
        email: email.email,
        is_primary: email.pivot.is_primary,
        is_verified: email.pivot.is_verified,
        _delete: false  // For å markere sletting
    })) || []
});

const loading = ref(false);

// Håndter telefonnumre
const addPhone = () => {
    form.value.phones.push({ 
        number: '',
        country: '',
        primary: form.value.phones.filter(p => !p._delete).length === 0,
        _new: true
    });
};

const removePhone = (index) => {
    const phone = form.value.phones[index];
    if (phone._new) {
        form.value.phones.splice(index, 1);
    } else {
        phone._delete = true;
    }
    
    // Oppdater primær hvis nødvendig
    const activePhones = form.value.phones.filter(p => !p._delete);
    if (activePhones.length > 0 && !activePhones.some(p => p.is_primary)) {
        activePhones[0].is_primary = true;
    }
};

const setPrimaryPhone = (index) => {
    form.value.phones.forEach((phone, i) => {
        if (!phone._delete) {
            phone.is_primary = i === index;
        }
    });
};

// Håndter e-postadresser
const addEmail = () => {
    form.value.emails.push({ 
        email: '', 
        is_primary: form.value.emails.filter(e => !e._delete).length === 0,
        is_verified: false,
        _new: true
    });
};

const removeEmail = (index) => {
    const email = form.value.emails[index];
    if (email._new) {
        form.value.emails.splice(index, 1);
    } else {
        email._delete = true;
    }
    
    // Oppdater primær hvis nødvendig
    const activeEmails = form.value.emails.filter(e => !e._delete);
    if (activeEmails.length > 0 && !activeEmails.some(e => e.is_primary)) {
        activeEmails[0].is_primary = true;
    }
};

const setPrimaryEmail = (index) => {
    form.value.emails.forEach((email, i) => {
        if (!email._delete) {
            email.is_primary = i === index;
        }
    });
};

const handleSubmit = async () => {
    try {
        loading.value = true;
        
        // Forbered data for backend
        const formData = {
            ...form.value,
            phones: form.value.phones.map(phone => ({
                id: phone.id,
                number: phone.number,
                type: phone.type,
                is_primary: phone.is_primary,
                _delete: phone._delete,
                _new: phone._new
            })),
            emails: form.value.emails.map(email => ({
                id: email.id,
                email: email.email,
                is_primary: email.is_primary,
                _delete: email._delete,
                _new: email._new
            }))
        };
        
        const response = await axios.put('/api/user', formData);
        emit('update:profile', response.data);
        emit('close');
        
        notify({
            group: "success",
            title: trans('common.success'),
            text: trans('profile.success.update')
        });
    } catch (error) {
        console.error('Error updating profile:', error);
        notify({
            group: "error",
            title: trans('common.error'),
            text: trans('profile.error.update')
        });
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Fornavn -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                {{ trans('profile.fields.given_name') }}
            </label>
            <input 
                type="text"
                v-model="form.given_name"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                required
            >
        </div>

        <!-- Etternavn -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                {{ trans('profile.fields.family_name') }}
            </label>
            <input 
                type="text"
                v-model="form.family_name"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                required
            >
        </div>

        <!-- Mellomnavn -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                {{ trans('profile.fields.additional_name') }}
            </label>
            <input 
                type="text"
                v-model="form.additional_name"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
        </div>

        <!-- Telefonnumre -->
        <div>
            <div class="flex justify-between items-center mb-2">
                <label class="block text-sm font-medium text-gray-700">
                    {{ trans('profile.fields.phones') }}
                </label>
                <button 
                    type="button"
                    @click="addPhone"
                    class="text-sm text-blue-600 hover:text-blue-800">
                    + {{ trans('profile.actions.add_phone') }}
                </button>
            </div>
            
            <div v-for="(phone, index) in form.phones" 
                 :key="index" 
                 v-show="!phone._delete"
                 class="flex items-center gap-2 mb-2">
                <div class="flex-grow grid grid-cols-4 gap-2">
                    <input 
                        type="text"
                        v-model="phone.country"
                        class="col-span-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        :placeholder="trans('profile.placeholders.country_code')"
                    >
                    <input 
                        type="tel"
                        v-model="phone.number"
                        class="col-span-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        :placeholder="trans('profile.placeholders.phone')"
                    >
                </div>
                <button 
                    v-if="!phone.primary"
                    type="button"
                    @click="setPrimaryPhone(index)"
                    class="text-sm text-blue-600 hover:text-blue-800">
                    {{ trans('profile.actions.make_primary') }}
                </button>
                <button 
                    type="button"
                    @click="removePhone(index)"
                    class="text-sm text-red-600 hover:text-red-800">
                    {{ trans('common.remove') }}
                </button>
                <span v-if="phone.primary" class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                    {{ trans('profile.primary') }}
                </span>
                <span v-if="phone.verified_at" class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
                    {{ trans('profile.verified') }}
                </span>
            </div>
        </div>

        <!-- E-post -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                {{ trans('profile.fields.email') }}
            </label>
            <input 
                type="email"
                v-model="form.email"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                required
            >
        </div>

        <!-- Knapper -->
        <div class="flex justify-end space-x-3 pt-4">
            <button
                type="button"
                @click="$emit('close')"
                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
                {{ trans('common.cancel') }}
            </button>
            <button
                type="submit"
                :disabled="loading"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
            >
                {{ loading ? trans('common.saving') : trans('common.save') }}
            </button>
        </div>
    </form>
</template> 