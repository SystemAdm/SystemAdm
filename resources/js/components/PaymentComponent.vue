<template>
    <div>
        <div class="bg-white w-1/3 p-3" id="card-element"></div>
        <div class="flex">
            <button 
                class="py-2 px-3 bg-green-700" 
                @click="handlePayment" 
                :disabled="loading"
                :class="{'text-gray-400 bg-green-400': loading}"
            >
                Pay
            </button>
            <button 
                class="py-2 px-3 bg-red-700" 
                @click="$emit('wantToPay', false)"
            >
                Abort
            </button>
        </div>
    </div>
</template>

<script>
import { loadStripe } from '@stripe/stripe-js';
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
        await this.initializeStripeElements();
    },
    methods: {
        async loadPayment() {
            this.stripe = await loadStripe(import.meta.env.VITE_STRIPE_PUBLISHABLE_KEY);
        },

        async initializeStripeElements() {
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

        async handlePayment() {
            this.loading = true;
            
            try {
                const result = await this.processStripePayment();
                if (result.success) {
                    await this.processMembership();
                }
            } catch (error) {
                console.error('Payment process failed:', error);
                this.showErrorNotification();
            } finally {
                this.loading = false;
                this.$emit('wantToPay', false);
                await this.loadPayment();
            }
        },

        async processStripePayment() {
            const { error, paymentIntent } = await this.stripe.confirmCardPayment(this.clientSecret, {
                payment_method: { card: this.cardElement }
            });

            if (error) {
                this.showNotification('error', 'Critical', 'Payment failed');
                console.error('Payment failed:', error);
                return { success: false };
            }

            this.showNotification('success', 'Success', 'Payment successful');
            console.log('Payment successful:', paymentIntent);
            return { success: true, paymentIntent };
        },

        async processMembership() {
            try {
                const response = await axios.post('/api/memberships', { success: true });
                this.showNotification('success', 'Success', 'Successfully renewed membership for 1 year');
                console.log('Emitting paid event with membership data:', response.data);
                this.$emit('paid', response.data);
            } catch (error) {
                console.error("Error in membership processing:", error);
                this.showNotification('error', 'Error', 'Failed to renew membership');
                throw error;
            }
        },

        showNotification(group, title, text) {
            this.$notify({ group, title, text }, 4000);
        }
    },
};
</script>
