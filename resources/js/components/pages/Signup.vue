<!-- Signup.vue -->
<template>
    <div class="fixed z-50 inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full px-4 text-black">
        <div class="m-5 pt-5 relative mx-auto shadow-xl rounded-md bg-white max-w-lg">
            <div class="p-6 pt-0 text-center">
                <h1 class="text-2xl font-extrabold text-black">Create new user</h1>
                <div class="px-8 pt-6">
                    <!-- PHONE -->
                    <PhoneComponent v-if="step === 1" :hasErrors="errors" @sendErrors="sendErrors" @selections="users" @close="close" @email="step = 1"></PhoneComponent>
                    <EmailComponent v-if="step === 2" :secondary="!primary" :hasErrors="errors" @sendErrors="sendErrors" @close="close" @phone="step = 0"></EmailComponent>
                    <SelectNameComponent v-if="step === 3 && selection != null" :selection="selection" @selectUser="selectUser" @sendErrors="sendErrors" :hasErrors="errors" @close="close"></SelectNameComponent>
                    <PasswordCheckComponent v-if="step === 4" :hasErrors="errors" @sendErrors="sendErrors" @close="close" @success="passwordCorrect" :user="this.selected"></PasswordCheckComponent>
                    <NameComponent v-if="step === 5 && selected === null" :hasErrors="errors" @sendErrors="sendErrors" :phone="phone" @user="user" @close="close"></NameComponent>
                    <SelectAgeComponent v-if="step === 6" :hasErrors="errors" @sendErrors="sendErrors" @close="close"></SelectAgeComponent>
                    <FamilyPhoneComponent v-if="step === 7" :hasErrors="errors" @sendErrors="sendErrors" @close="close" @familyPhone="setFamilyPhone"></FamilyPhoneComponent>
                    <FamilyNameComponent v-if="step === 8" :has-errors="errors" @sendErrors="sendErrors" @close="close" @familyName="setFamilyName"></FamilyNameComponent>
                    <SelectCreationComponent v-if="step === 9" @viewQr="viewQr" @close="close" @register="step = 5"></SelectCreationComponent>
                    <EmailComponent v-if="step === 10" :secondary="!primary" :hasErrors="errors" @sendErrors="sendErrors" @close="close"></EmailComponent>
                    <PasswordComponent v-if="step === 11" :hasErrors="errors" @sendErrors="sendErrors" @close="close"></PasswordComponent>
                    <SummaryAgreement v-if="step === 12"></SummaryAgreement>
                    <QrCode v-if="option === 'qr' && selected != null" :selectedUser="selected" @close="close"></QrCode>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import QrCode from "../modals/QrCode.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import PhoneComponent from "../PhoneComponent.vue";
import SelectNameComponent from "../SelectName.vue";
import NameComponent from "../NameComponent.vue";
import SelectCreationComponent from "../SelectCreationComponent.vue";
import SelectAgeComponent from "../SelectAgeComponent.vue";
import EmailComponent from "../EmailComponent.vue";
import PasswordComponent from "../PasswordComponent.vue";
import PasswordCheckComponent from "../PasswordCheckComponent.vue";
import FamilyPhoneComponent from "../FamilyPhoneComponent.vue";
import FamilyNameComponent from "../FamilyNameComponent.vue";
import SummaryAgreement from "../SummaryAgreement.vue";

export default {
    name: 'SignupPage',
    components: {
        SummaryAgreement,
        FamilyNameComponent,
        FamilyPhoneComponent,
        PasswordCheckComponent,
        PasswordComponent,
        EmailComponent,
        SelectAgeComponent,
        SelectCreationComponent,
        NameComponent,
        SelectNameComponent,
        PhoneComponent,
        FontAwesomeIcon,
        QrCode
    },
    props: {
        me: {
            type: Object,
            required: false,
        }
    },
    data() {
        return {
            modalOpen: false, // new
            step: 1,
            selection: null, // new
            selected: null, // new
            firstname: null,
            middlename: null,
            lastname: null,
            phone: null,
            option: null,
            errors: {
                phone: false,
                email: false,
                email_confirm: false,
                password: false,
                password_confirm: false,
                firstname: false,
                lastname: false,
                middlename: false,
                select_name: false,
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
        // STEP 0 - Phone form
        users(value) {
            this.phone = value.phone;
            this.selection = value.data;
            if (Object.keys(value.data).length === 1) {
                // Phone number is valid; No users found
                this.step = 3;
                // 0 -> 3; Phone -> Name
            } else {
                // One or more users is found; Show selection
                this.step = 1;
                // 0 -> 1; Phone -> SelectName
            }
        },
        // STEP 2
        selectUser(value) {
            if (value === 0) {
                // No user selected; Create a new user
                this.step = 3;
                // 1 -> 3, SelectName -> Name
            } else {
                // User found
                this.selected = value;
                if (this.selected.active) {
                    // User has an account protected
                    this.step++;
                    // 1 -> 2, SelectName -> PasswordCheck
                } else {
                    // User is not protected
                    this.step = 4;
                    // 1 -> 4, Phone -> FamilyPhone
                }
            }
        },
        // STEP 2 - Require if password is set! Password form
        passwordCorrect(){
            // Correct password given
            this.step = 99;
            this.option = "qr";
            // 2 -> 4; Password -> FamilyPhone
        },
        // STEP 3 - Name form
        user(value) {
            this.selected = value;
            // User saved
            this.step = 4;
            // 3 -> 4; Name -> FamilyPhone
        },
        // Optional STEP 6 - View QR code
        viewQr() {
            this.option = 'qr';
            this.step = 99;
        },
        // STEP 7 - Age form
        birth(value){
            this.birthday = value;
            this.step++;
            // 7 -> 8; Age -> E-mail
        },

        close() {
            this.selected = null;
            this.selection = null;
            this.options = null;
            this.step = 0;
        },
        sendErrors(value) {
            for (let key in value) {
                if (value.hasOwnProperty(key)) {
                    this.errors[key] = value[key];
                }
            }
        },
        closeModal() {
            this.modalOpen = false
        },

    },
};
</script>

