<!-- Signup.vue -->
<template>
    <div class="fixed z-50 inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full px-4 text-black">
        <div class="pt-5 relative top-40 mx-auto shadow-xl rounded-md bg-white max-w-lg">
            <div class="p-6 pt-0 text-center">
                <h1 class="text-2xl font-extrabold text-black">Create new user</h1>
                <form class=" px-8 pt-6 pb-2 mb-4">
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
                            you.<br/><span class="font-bold">Are you:</span></label>
                        <div class="justify-self-start" v-for="name in names" :key="name.id">
                            <div class="text-gray-500 font-bold flex items-center" @click="this.name = name.id">
                                <input
                                    class="hidden"
                                    type="radio"
                                    name="restriction"
                                    v-model="this.name"
                                    :value="name.id"
                                >
                                <div
                                    :class="[
                'border-4 p-1 transition-all duration-300',
                this.name === name.id ? 'border-blue-500' : 'border-transparent'
            ]" class="justify-items-center"
                                >
                                    <img
                                        :src="name.profile.image"
                                        alt="User Avatar"
                                        class="w-12 h-12 rounded-full object-cover"
                                    >
                                    <span class="text-sm ml-2">{{ name.fullname }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="justify-self-start">
                            <label class="block text-black font-bold">
                                <input class="mr-2 leading-tight" type="radio" name="restriction"
                                       id="attending_crew" v-model="this.name" :value="0">
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
                    <div v-if="step===3">
                        <div class="flex justify-center gap-2">
                            <div class="text-gray-500 font-bold flex items-center" @click="this.option = 'qr'">
                                <input
                                    class="hidden"
                                    type="radio"
                                    name="restriction"
                                    v-model="this.option"
                                    value="qr"
                                >
                                <div
                                    :class="[
                'border-4 p-1 transition-all duration-300',
                this.option === 'qr' ? 'border-blue-500' : 'border-transparent'
            ]" class="justify-items-center"
                                >
                                    <font-awesome-icon class="size-20" :icon="['fas', 'qrcode']"/>
                                    <div class="text-sm ml-2">Show my QR code</div>
                                </div>
                            </div>
                            <div class="text-gray-500 font-bold flex items-center" @click="this.option = 'register'">
                                <input
                                    class="hidden"
                                    type="radio"
                                    name="restriction"
                                    v-model="this.option"
                                    value="register"
                                >
                                <div
                                    :class="[
                'border-4 p-1 transition-all duration-300',
                this.option === 'register' ? 'border-blue-500' : 'border-transparent'
            ]" class="justify-items-center"
                                >
                                    <font-awesome-icon class="size-20" :icon="['fas', 'id-card']"/>
                                    <div class="text-sm ml-2">Login or register an account</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="step === 4" class="text-left">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="lastname">
                            Birthday
                            <span class="block text-gray-400 text-sm">If not set, you will be treated as less than 13 years old</span>
                        </label>
                        <!-- INPUT TEXT PHONE -->
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="birthday"
                            type="date"
                            :min="minDate"
                            placeholder="Birthday"
                            v-model="this.birthday"
                            :class="{'border-red-700':error.birthday}"
                            v-on:focus="error.birthday = false">
                        <span class="ml-3 mt-2 block text-red-700" v-if="error.birthday">{{ error.birthday }}</span>
                    </div>
                    <div v-if="step === 5" class="text-left">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                            E-mail address <span class="text-red-700">*</span>
                            <span class="block text-gray-400 text-sm">We will only send you critical, but insensitive information. Ex. reset password</span>
                        </label>
                        <!-- INPUT TEXT PHONE -->
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="email"
                            type="email"
                            placeholder="E-mail address"
                            v-model="this.email"
                            required
                            :class="{'border-red-700':error.email}"
                            v-on:focus="error.email = false">
                        <span class="ml-3 mt-2 block text-red-700" v-if="error.email">{{ error.email }}</span>
                        <label class="block text-gray-700 text-sm font-bold mb-2 mt-5" for="email_confirm">
                            E-mail address confirmation<span class="text-red-700">*</span>
                            <span
                                class="block text-gray-400 text-sm">Please confirm you e-mail address by retyping it</span>
                        </label>
                        <!-- INPUT TEXT PHONE -->
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="email_confirm"
                            type="email"
                            placeholder="E-mail address confirmation"
                            v-model="this.email_confirm"
                            required
                            :class="{'border-red-700':error.email_confirm}"
                            v-on:focus="error.email_confirm = false">
                        <span class="ml-3 mt-2 block text-red-700" v-if="error.email_confirm">{{
                                error.email_confirm
                            }}</span>
                    </div>
                    <div v-if="step === 7" class="text-left">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                            Password <span class="text-red-700">*</span>
                            <span
                                class="block text-gray-400 text-sm">This is YOUR password, we will never ask for it,<br/>neither will anyone else without bad intentions</span>
                        </label>
                        <!-- INPUT TEXT PHONE -->
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="password"
                            type="password"
                            placeholder="Password"
                            v-model="this.password"
                            required
                            :class="{'border-red-700':error.password}"
                            v-on:focus="error.password = false">
                        <span class="ml-3 mt-2 block text-red-700" v-if="error.password">{{ error.password }}</span>
                        <label class="block text-gray-700 text-sm font-bold mb-2 mt-5" for="password_confirm">
                            Password confirmation<span class="text-red-700">*</span>
                            <span
                                class="block text-gray-400 text-sm">Please confirm your password by retyping it</span>
                        </label>
                        <!-- INPUT TEXT PHONE -->
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="password_confirm"
                            type="password"
                            placeholder="Password confirmation"
                            v-model="this.password_confirm"
                            required
                            :class="{'border-red-700':error.password_confirm}"
                            v-on:focus="error.password_confirm = false">
                        <span class="ml-3 mt-2 block text-red-700" v-if="error.password_confirm">{{
                                error.password_confirm
                            }}</span>
                    </div>
                    <!-- TODO
                    <div v-if="step === 1" class="text-left">
                        <div v-if="names.length === 0" v-for="name in names">
                            <div v-if="name != null">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    {{ name }}
                                    <input type="radio" name="user" :value="name.fullname">
                                </label>
                            </div>
                            <div v-else class="text-black">&lt;/Deleted User&gt;</div>
                        </div>
                        <div v-else class="text-black">&lt;/Deleted User&gt;</div>
                    </div>-->
                </form>
                <div class="block text-black mb-3">
                    All fields marked with <span class="text-red-700">*</span> is required
                </div>
                <button v-if="step >= 10" @click="next"
                        class="text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center mr-2">
                    Submit
                </button>
                <button v-else @click="next"
                        class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center mr-2">
                    Next
                </button>
                <button v-if="step !== 0" @click="prev"
                        class="text-white bg-yellow-600 hover:bg-yellow-800 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center mr-2">
                    Prev
                </button>
                <router-link to="/"
                             class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center">
                    Cancel
                </router-link>
                <span class="text-black">{{ this.step }}</span>
            </div>
        </div>
    </div>
    <QrCode modal-open="modal-open" @closeModal="closeModal"></QrCode>
</template>

<script>
import axios from "axios";
import {DateTime} from "luxon";
import QrCode from "../modals/QrCode.vue";

export default {
    name: 'SignupPage',
    components: {QrCode},
    data() {
        return {
            modalOpen: false,
            step: 3,
            firstname: null,
            middlename: null,
            lastname: null,
            phone: null,
            option: 'qr',
            error: {
                phone: false,
                email: false,
                email_confirm: false,
                password: false,
                password_confirm: false,
                firstname: false,
                lastname: false,
                middlename: false,
            },
            names: {
                0: {name: 'Anita Skorgan', id: 0},
                1: {name: 'Leif Juster', id: 1}
            },
            return: null,
            minDate: null,
            birthday: null,
            email: null,
            email_confirm: null,
            password: null,
            password_confirm: null,
            name: null,
        }
    },
    methods: {
        closeModal() {
            this.modalOpen = false
        },
        confirmAction() {
            // Handle your confirmation logic here
            console.log('Action confirmed.');
        },
        prev() {
            if (this.step === 1) {
                this.step--;
            }
        },
        next() {
            if (this.step === 4) {
                if (this.email == null) {
                    this.error.email = "E-mail address is required";
                }
                if (this.email === this.email_confirm) {
                    this.step++;
                }
                axios.post('/api/users/validate_email', {email: this.email}).then(response => {
                    if (response.data === 0) {
                        this.error.email = "Invalid e-mail address";
                    } else if (response.data === 1) {
                        this.step = this.step + 2;
                    } else {
                        this.names = response.data;
                        this.step++;
                    }
                })
                this.error.email_confirm = 'E-mail address confirmation does not match';
            }
            if (this.step === 3) {
                if (this.option === 'qr') {
                    this.modalOpen = true;
                } else {
                    this.step++;
                }
            }
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
                    this.step++;
                }
                // TODO
            }
            if (this.step === 1) {
                if (this.name > 0) {
                    this.step++;
                }
                this.step++;
            }
            if (this.step === 0) {
                if (this.phone != null) {
                    axios.post('/api/users/validate_phone', {phone: this.phone}).then(response => {
                        if (response.data === 0) {
                            this.error.phone = "Invalid phone number";
                        } else if (response.data === 1) {
                            this.step = this.step + 2;
                        } else {
                            this.names = response.data;
                            this.step++;
                        }
                    });
                } else {
                    this.error.phone = true;
                }
            }
        }
    },
    mounted() {
        this.minDate = DateTime.now().minus({years: 10}).toISODate();
    },
};
</script>

