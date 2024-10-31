<!-- App.vue -->
<template>
    <div class="container">
        <Navbar/>
        <div class="p-5">
            <router-view/> <!-- This is crucial to display the routed component -->
        </div>
    </div>
</template>

<script>
import Navbar from '../Navbar.vue';

export default {
    components: {
        Navbar,
    },
};

</script>
