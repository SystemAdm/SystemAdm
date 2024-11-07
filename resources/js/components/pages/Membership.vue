<template>
    <div :class="{'bg-green-700': can('events.create')}">Membership status of {{ user.fullname }}</div>

    <div v-if="!wantToPay">
        <button type="button" class="py-2 px-3" @click="wantToPay = true">Pay for my membership</button>
    </div>

    <!-- Listen for both `wantToPay` and `paid` events -->
    <PaymentComponent
        v-if="clientSecret != null && wantToPay"
        @wantToPay="wantToPayEmit"
        @paid="paid"
        :client-secret="clientSecret">
    </PaymentComponent>

    <div v-for="membership in memberships" :key="membership.id">
        <div>{{ membership.validDate }} <span v-if="membership.valid">active</span></div>
    </div>
</template>

<script>
import axios from "axios";
import PaymentComponent from "../PaymentComponent.vue";
import {can} from "laravel-permission-to-vuejs";

export default {
    name: "Membership",
    components: {PaymentComponent},
    data() {
        return {
            user: {},
            memberships: [],
            loading: false,
            clientSecret: null,
            wantToPay: false,
        };
    },
    methods: {
        can,
        async fetch() {
            this.loading = true;
            try {
                const res = await axios.get("/api/memberships");
                this.memberships = res.data.memberships;
                this.user = res.data.user;
            } finally {
                this.loading = false;
            }
        },
        wantToPayEmit(boolean) {
            this.wantToPay = boolean;
        },
        paid(membership) {
            // Add the new membership to the list
            console.log('Received new membership:', membership);
            //this.memberships.push(membership);
            this.memberships = [...this.memberships, membership];
        },
    },
    async mounted() {
        await this.fetch();

        try {
            const res = await axios.post('/api/memberships/pay');
            this.clientSecret = res.data.clientSecret;
        } catch (error) {
            console.error("Error fetching client secret:", error);
        }
    },
};
</script>
