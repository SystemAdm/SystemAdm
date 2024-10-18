<!-- App.vue -->
<template>
    <Navbar />
    <div class="p-5">
        <router-view /> <!-- This is crucial to display the routed component -->
    </div>
</template>

<script>
import Navbar from './Navbar.vue';

export default {
    components: {
        Navbar,
    },
};

</script>
