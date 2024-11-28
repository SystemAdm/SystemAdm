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
                @click="emit('wantToPay', false)"
            >
                Abort
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { loadStripe } from '@stripe/stripe-js';
import axios from "axios";
import { useNotification } from '@kyvg/vue3-notification';

const { notify } = useNotification();

const props = defineProps({
    clientSecret: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['wantToPay', 'paid']);

const stripe = ref(null);
const cardElement = ref(null);
const elements = ref(null);
const loading = ref(false);

const loadPayment = async () => {
    stripe.value = await loadStripe(import.meta.env.VITE_STRIPE_PUBLISHABLE_KEY);
};

const initializeStripeElements = async () => {
    elements.value = stripe.value.elements();
    cardElement.value = elements.value.create('card', {
        hidePostalCode: true,
        style: {
            base: {
                fontSize: '16px',
                color: '#32325d',
            },
        },
    });
    cardElement.value.mount('#card-element');
};

const showNotification = (group, title, text) => {
    notify({ group, title, text }, 4000);
};

const processStripePayment = async () => {
    const { error, paymentIntent } = await stripe.value.confirmCardPayment(props.clientSecret, {
        payment_method: { card: cardElement.value }
    });

    if (error) {
        showNotification('error', 'Critical', 'Payment failed');
        console.error('Payment failed:', error);
        return { success: false };
    }

    showNotification('success', 'Success', 'Payment successful');
    console.log('Payment successful:', paymentIntent);
    return { success: true, paymentIntent };
};

const processMembership = async () => {
    try {
        const response = await axios.post('/api/memberships', { success: true });
        showNotification('success', 'Success', 'Successfully renewed membership for 1 year');
        console.log('Emitting paid event with membership data:', response.data);
        emit('paid', response.data);
    } catch (error) {
        console.error("Error in membership processing:", error);
        showNotification('error', 'Error', 'Failed to renew membership');
        throw error;
    }
};

const handlePayment = async () => {
    loading.value = true;
    
    try {
        const result = await processStripePayment();
        if (result.success) {
            await processMembership();
        }
    } catch (error) {
        console.error('Payment process failed:', error);
        showNotification('error', 'Error', 'Payment process failed');
    } finally {
        loading.value = false;
        emit('wantToPay', false);
        await loadPayment();
    }
};

onMounted(async () => {
    await loadPayment();
    await initializeStripeElements();
});
</script>
