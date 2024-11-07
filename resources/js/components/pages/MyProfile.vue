<template>
    <div v-if="loading === false">
        {{ user.fullname}}
    </div>
</template>
<script>
import axios from "axios";
export default {
    name: 'MyProfile',
    data() {
        return {user: null, loading: true}
    },
    methods: {
        fetch() {
            axios.get('/api/users/me').then(response => {
                this.user = response.data;
                this.loading = false;
            });
        }
    }, mounted() {
        this.fetch();
    }
}
</script>
