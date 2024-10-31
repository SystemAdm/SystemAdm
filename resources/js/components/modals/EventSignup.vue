<template>
    <div v-if="modalOpen" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full px-4">
        <div class="relative top-40 mx-auto shadow-xl rounded-md bg-white max-w-lg">
            <div class="flex justify-end p-2">
                <button @click="closeModal" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-3xl p-1.5 ml-auto inline-flex items-center">
                    &times;
                </button>
            </div>
            <div class="p-6 pt-0 text-center">
                <h1 class="text-2xl font-extrabold text-black">Sign me up!</h1>
                <form class=" px-8 pt-6 pb-8 mb-4">
                    <div v-if="step === 0" class="text-left">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="lastname">
                            Phone number <span class="text-red-700">*</span>
                        </label>
                        <!-- INPUT TEXT PHONE -->
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="lastname"
                            type="text"
                            placeholder="Phone"
                            v-model="this.phone"
                            required
                            :class="{'border-red-700':error.phone}"
                            v-on:focus="error.phone = false">
                        <span class="ml-3 mt-2 block text-red-700" v-if="error.phone">{{ error.phone }}</span>
                    </div>
                    <div v-if="step === 1">
                        <label>From your phone number, we found a registration on our site! We need a confirmation from
                            you. Are you:</label>
                        <div class="justify-self-start" v-for="found in names" :key="found.id">
                            <label class="block text-gray-500 font-bold">
                                <input class="mr-2 leading-tight" type="radio" name="user"
                                       v-model="this.name" :value="found.id">
                                <span class="text-sm">{{ found.fullname }}</span>
                            </label>
                        </div>
                        <div class="justify-self-start">
                            <label class="block text-black font-bold">
                                <input class="mr-2 leading-tight" type="radio" name="user"
                                       v-model="this.name" :value="0">
                                <span class="text-sm">Nah. I am someone else</span>
                            </label>
                        </div>
                    </div>
                    <div v-if="step === 2">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="firstname">
                                Firstname <span class="text-red-700">*</span>
                            </label>
                            <!-- INPUT NAME FIRST -->
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="firstname"
                                type="text"
                                placeholder="Firstname"
                                v-model="this.firstname">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="middlename">
                                Middlename <span class="text-gray-400">(optional)</span>
                            </label>
                            <!-- INPUT NAME MIDDLE -->
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="middlename"
                                type="text"
                                placeholder="Middlename"
                                v-model="this.middlename">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="lastname">
                                Lastname <span class="text-red-700">*</span>
                            </label>
                            <!-- INPUT NAME LAST -->
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="lastname"
                                type="text"
                                placeholder="Lastname"
                                v-model="this.lastname">
                        </div>
                    </div>
                </form>
                <button v-if="step >= 9" @click="next"
                        class="text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center mr-2">
                    Submit
                </button>
                <button v-else @click="next"
                        class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center mr-2">
                    <span v-if="loading"><FontAwesomeIcon class="animate-spin" :icon="faCog()"></FontAwesomeIcon>&nbsp;</span>Next
                </button>
                <button v-if="step !== 0" @click="prev"
                        class="text-white bg-yellow-600 hover:bg-yellow-800 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center mr-2">
                    Prev
                </button>
                <button @click="closeModal"
                        class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center">
                    Cancel
                </button>
                <span class="text-black">{{ this.step }}</span>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {faCog} from "@fortawesome/free-solid-svg-icons";

export default {
    components: {FontAwesomeIcon},
    props: {
        modalOpen: {
            type: Boolean,
            required: true
        },
        selected: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
            loading: false,
            step: 0,
            phone: null,
            firstname: null,
            middlename: null,
            lastname: null,
            error: {
                phone: false,
                firstname: false,
                middlename: false,
                lastname: false,
            },
            names: [],
            name: 8,
        }
    },
    methods: {
        faCog() {
            return faCog
        },
        next() {
            if (this.step === 2) {
                if (this.firstname == null || this.firstname === '') {
                    this.error.firstname = 'Firstname is required';
                } else if (this.firstname.length < 2) {
                    this.error.firstname = 'Firstname must contain more than 2 characters';
                }
                if (this.lastname == null || this.lastname === '') {
                    this.error.lastname = 'Lastname is required';
                } else if (this.lastname.length < 2) {
                    this.error.lastname = 'Lastname must contain more than 2 characters';
                }
                if (this.middlename != null && this.middlename !== '') {
                    if (this.middlename.length < 2) {
                        this.error.middlename = 'Middlename must contain more than 2 characters';
                    }
                }
                if (!this.error.firstname || !this.error.firstname || !this.error.middlename) {
                    this.confirmAction()
                }
            }
            if (this.step === 1) {
                if (this.name !== 0) {
                    this.confirmAction()
                } else {
                    this.step++;
                }
            }
            if (this.step === 0) {
                if (this.phone != null) {
                    this.loading = true;
                    axios.post('/api/users/validate_phone', {phone: this.phone}).then(response => {
                        if (response.data === 0) {
                            this.error.phone = "Invalid phone number";
                        } else if (response.data === 1) {
                            this.step = this.step + 2;
                        } else {
                            this.names = response.data;
                            this.step++;
                        }
                        this.loading = false;
                    });
                } else {
                    this.error.phone = true;
                }
            }
        },
        prev() {

        },
        closeModal() {
            this.step = 0;
            this.$emit('update:modalOpen', false); // Emit the event to update parent state
        },

        async confirmAction() {
            // Handle your confirmation logic here
            if (this.name === 0) {
                this.loading = true;
                await axios.post('/api/users', {
                    firstname: this.firstname,
                    middlename: this.middlename,
                    lastname: this.lastname,
                    phone: this.phone
                }).then(response => {
                    this.name = response.data.id;
                    this.loading = false;
                });
            }

            if (this.name !== 0) {
                this.loading = true;
                await axios.post('/api/events/' + this.selected + '/register', {user: this.name}).then(response => {
                    console.log('user joined event');
                    this.loading = false;
                })
                this.closeModal();
            }
        },
    }
};
</script>
<style scoped>

</style>
