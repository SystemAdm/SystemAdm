<template>
    <div v-show="required" class="block text-black m-3 text-sm">
        All fields marked with <span class="text-red-700">*</span> is required
    </div>
    <div class="flex flex-wrap justify-center gap-2">
        <button type="button" v-if="prev && !trashed" @click="$emit('back')"
                class="py-2 px-3 text-white bg-yellow-600 hover:bg-yellow-800 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-base inline-flex items-center   text-center">
            <font-awesome-icon :icon="['fas', 'arrow-left']" class="mr-2"/>
            Prev
        </button>
        <button type="button" @click="$emit('close')"
            class="py-2 px-3 text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-base inline-flex items-center  text-center ">
            <font-awesome-icon :icon="['fas','xmark']" class="mr-2"/>
            Close
        </button>
        <button type="button" v-if="next && !trashed" @click="$emit('go')"
                class="py-2 px-3 text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-base inline-flex items-center  text-center">
            Next
            <font-awesome-icon :icon="['fas', 'arrow-right']" class="pl-2"/>
        </button>
        <button type="button" v-else-if="submit && !trashed" @click="$emit('go')"
                class="py-2 px-3 text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-base inline-flex items-center text-center ">
            Submit
            <font-awesome-icon :icon="['fas', 'arrow-right']" class="pl-2"/>
        </button>
        <button type="button" v-if="this.delete && !trashed" @click="$emit('delete')"
                class="py-2 px-3 text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-base inline-flex items-center  text-center ">
            <font-awesome-icon :icon="['fas', 'trash-can']" class="pr-2"/>Delete

        </button>
        <button type="button" v-if="trashed" @click="$emit('force')"
                class="py-2 px-3 text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-base inline-flex items-center  text-center">
            <font-awesome-icon :icon="['fas', 'trash-can']" class="pr-2"/>Force Delete

        </button>
        <button type="button" v-if="trashed" @click="$emit('restore')"
                class="py-2 px-3 text-white bg-orange-600 hover:bg-orange-700 focus:ring-4 focus:ring-orange-300 font-medium rounded-lg text-base inline-flex items-center text-center">
            <font-awesome-icon :icon="['fas', 'trash-can-arrow-up']" class="pr-2"/>Restore

        </button>
    </div>
</template>
<script>
export default {
    props: {
        step: Number,
        next: Boolean,
        prev: Boolean,
        required: Boolean,
        submit: Boolean,
        delete: Boolean,
        trashed: Boolean,
    },
    emits: ['go', 'close', 'back','delete','force','restore'],
}
</script>
