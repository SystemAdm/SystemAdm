<template>

</template>
<script setup>
import axios from "axios";
import {onMounted} from "vue";
import {useRoute} from "vue-router";

const route = useRoute();

const providerData = await axios.post("/api/authorize/" + route.params.provider + "/callback", {code: route.query.code}).then(res => {
    console.log(res);
});

onMounted(() => {
    console.log(providerData);
})
</script>
