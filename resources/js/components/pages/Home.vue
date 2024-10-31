<!-- Home.vue -->
<template>
    <div class="py-4 px-4">
        <h1>Welcome to the Home Page</h1>
        <p>This is the home component</p>
    </div>
    <NextEvent/>
</template>

<script>
import NextEvent from "../NextEvent.vue";
export default {
    name: 'Home',
    components: {
        NextEvent,
    },
    mounted() {
        console.log('Home component mounted');
    },
};
</script>

