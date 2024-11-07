<template>
    <div>
        <div class="bg-white w-1/3 p-3" id="card-element"></div>
        <div class="flex">
            <button class="py-2 px-3 bg-green-700" @click="handlePayment" v-bind:disabled="loading"
                    :class="{'text-gray-400 bg-green-400':loading}">Pay
            </button>
            <button class="py-2 px-3 bg-red-700" @click="$emit('wantToPay', false)">Abort</button>
        </div>
    </div>
</template>

<script>
import {loadStripe} from '@stripe/stripe-js';
import axios from "axios";

export default {
    emits: ['wantToPay', 'paid'],
    props: {
        clientSecret: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            stripe: null,
            cardElement: null,
            elements: null,
            loading: false,
        };
    },
    async mounted() {
        await this.loadPayment();

        // Initialize Stripe Elements and Card Element
        this.elements = this.stripe.elements();
        this.cardElement = this.elements.create('card', {
            hidePostalCode: true,
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                },
            },
        });
        this.cardElement.mount('#card-element');
    },
    methods: {
        async loadPayment() {
            // Load Stripe with the publishable key from the environment
            this.stripe = await loadStripe(import.meta.env.VITE_STRIPE_PUBLISHABLE_KEY);
        },
        async handlePayment() {
            this.loading = true;
            try {
                const {error, paymentIntent} = await this.stripe.confirmCardPayment(this.clientSecret, {
                    payment_method: {
                        card: this.cardElement,
                    },
                });

                if (error) {
                    this.$notify({
                        group: "error",
                        title: "Critical",
                        text: "Payment failed",
                    }, 4000);
                    console.error('Payment failed', error);
                } else {
                    this.$notify({
                        group: "success",
                        title: "Success",
                        text: "Payment successful",
                    }, 4000);
                    console.log('Payment successful', paymentIntent);

                    // Only send the axios post request if payment was successful
                    try {
                        const response = await axios.post('/api/memberships', {success: true});
                        this.$notify({
                            group: "success",
                            title: "Success",
                            text: "Successfully renewed membership for 1 year",
                        }, 4000);

                        // Emit 'paid' event only after axios request completes successfully
                        console.log('Emitting paid event with membership data:', response.data); // Log for debugging
                        this.$emit('paid', response.data);
                    } catch (axiosError) {
                        console.error("Error in axios post", axiosError);
                        this.$notify({
                            group: "error",
                            title: "Error",
                            text: "Failed to renew membership",
                        }, 4000);
                    }
                }
            } finally {
                // Always reset loading state and emit 'wantToPay' after everything completes
                this.loading = false;
                this.$emit('wantToPay', false);
                await this.loadPayment();
            }
        }
    },
};
</script>
