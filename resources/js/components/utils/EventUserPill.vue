<template>
    <font-awesome-icon icon="fa-spinner" spin class="ml-2" v-if="loading"/>
    <div class="flex justify-between border-t-2 border-gray-500 py-1">
        <div class="flex">
            <img v-if="user.profile != null" :src="user.profile?.image" alt=""
                 class="h-6 rounded-full max-w-6">
            <span class="ml-2 overflow-hidden h-6">{{ user.full_name }}</span>
        </div>
        <div class="text-nowrap">
            <span>
        <abbr title="View Profile"><font-awesome-icon icon="fa-id-card-clip" class="text-blue-500 mr-2"/></abbr>
    </span>
            <span v-if="col === 'Registered' || col === 'RegisteredCrew'" @click="attend">

        <abbr title="Welcome"><font-awesome-icon icon="fa-bell-concierge" class="text-green-500 mr-2"/></abbr>
    </span>
            <span v-if="(isAttending) && !(isInsider) && (col === 'Attending' || col === 'AttendingCrew')"
                  @click="inside">

        <abbr title="Inside"><font-awesome-icon icon="fa-door-open" class="text-yellow-500 mr-2"/></abbr>
    </span>
            <span v-if="(isInsider) && (col === 'Insider' || col === 'InsiderCrew')" @click="leave">

        <abbr title="Leave"><font-awesome-icon icon="fa-door-closed" class="text-red-500 mr-2"/></abbr>
    </span>
            <span v-if="(isAttending) && (col === 'Attending' || col === 'AttendingCrew')" @click="unattend">

        <abbr title="Unattend"><font-awesome-icon icon="fa-triangle-exclamation" class="text-red-500 mr-2"/></abbr>
    </span>
            <span v-if="col === 'Registered' || col === 'RegisteredCrew'" @click="trash">

        <abbr title="Trash"><font-awesome-icon icon="fa-trash" class="text-red-500 mr-2"/></abbr>
    </span>
            <span>
        <abbr title="Ban user"><font-awesome-icon icon="fa-skull-crossbones" class="text-red-500 mr-2"/></abbr>
    </span>
        </div>
    </div>
</template>
<script setup>
import axios from "axios";
import {onMounted, ref} from "vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const props = defineProps({
    user: Object,
    col: String,
    event: Number,
    isAttending: Boolean,
    isInsider: Boolean,
    isCrew: Boolean,
    isRegistered: Boolean,
});
const loading = ref(false);
const emits = defineEmits(['update']);

async function attend() {
    //console.log(props.event, props.user,props.col);
    loading.value = true;
    try {
        const response = await axios.post('/api/admin/events/' + props.event + '/attend/', {user: props.user.id});
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
        emits('update', {action: 'attend', user: props.user});
    }
}

async function unattend() {
    loading.value = true;
    try {
        const response = await axios.post('/api/admin/events/' + props.event + '/unattend/', {user: props.user.id});
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
        emits('update', {action: 'unattend', user: props.user});
    }
}

async function inside() {
    loading.value = true;
    try {
        const response = await axios.post('/api/admin/events/' + props.event + '/inside/', {user: props.user.id});
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
        emits('update', {action: 'insider', user: props.user});
    }
}

async function leave() {
    loading.value = true;
    try {
        const response = await axios.post('/api/admin/events/' + props.event + '/leave/', {user: props.user.id});
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
        emits('update', {action: 'leave', user: props.user});
    }
}

async function trash() {
    loading.value = true;
    try {
        const response = await axios.post('/api/admin/events/' + props.event + '/trash/', {user: props.user.id});
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
        emits('update', {action: 'unregister', user: props.user});
    }
}

onMounted(() => {
    console.log(props.col)
})
</script>
<style scoped>
span {
    cursor: pointer;
}
</style>
