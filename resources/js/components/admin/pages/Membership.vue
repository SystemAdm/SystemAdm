<template>
    <div v-if="memberships != null" v-for="membership in memberships">
        {{ membership.user.fullname }}
    </div>
</template>
<script>
import axios from "axios";

export default {
    name: "Membership",
    data() {
        return {
            loading: true,
            memberships: null,
        }
    },
    methods: {
        fetch() {
            axios.get("/api/memberships").then(res => {
                this.memberships = res.data;
                this.loading = false;
            })
            console.log(this.loading)
        }
    },
    mounted() {
        this.fetch();
    }
}
</script>
