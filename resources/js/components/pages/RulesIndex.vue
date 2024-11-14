<template>
    <div class="ml-4">
        <div class="grid grid-cols-2">
            <h1 class="row-start mr-2 text-4xl font-bold">Rules:</h1>
        </div>
        <div class="mt-6" v-for="location in rules" :key="location.name">
            <h2 class="text-2xl font-bold">{{location.location_name}}</h2>
            <div class="border-t mt-0 pt-1 mb-6" v-for="rule in Object.values(location.rules)" :key="rule.id">
                <div>
                    <span class="font-bold">{{ rule.name }}</span>
                </div>
                <div>{{ rule.description }}</div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from "axios";

export default {
    components: {},
    props: {
        me: Object,
    },
    data() {
        return {
            rules: {},
        }
    },
    methods: {
        fetch() {
            axios.get("/api/admin/rules").then(res => {
                console.log("Fetched rules:", res.data);
                this.rules = res.data;
            });
        },
    },
    mounted() {
        this.fetch();
    }
}
</script>
