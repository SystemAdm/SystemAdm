<!-- Signup.vue -->
<template>
    <div class="fixed z-50 inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full px-4 text-black">
        <div class="m-5 pt-5 relative mx-auto shadow-xl rounded-md bg-white max-w-lg">
            <div class="p-6 pt-0 text-center">
                <h1 class="text-2xl font-extrabold text-black">Create new user</h1>
                
                <div class="px-8 pt-6">
                    <component 
                        :is="currentComponent"
                        v-bind="componentProps"
                        @sendErrors="errors => registration.errors = errors"
                        @close="reset"
                        @success="handleSuccess"
                        @back="step = prev.pop()"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
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
import EmailCheckComponent from "../EmailCheckComponent.vue";

export default {
    name: 'SignupPage',
    components: {
        EmailCheckComponent,
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
            STEPS: {
                PHONE: 1,
                EMAIL: 2,
                SELECT_NAME: 3,
                PASSWORD_CHECK: 4,
                NAME: 5,
                AGE: 6,
                GUARDIAN_PHONE: 7,
                GUARDIAN_EMAIL: 8,
                GUARDIAN_SELECT: 9,
                GUARDIAN_NAME: 10,
                CREATION_SELECT: 11,
                USER_EMAIL: 12,
                USER_PASSWORD: 13,
                CHILD_PHONE: 14,
                CHILD_EMAIL: 15,
                CHILD_SELECT: 16,
                CHILD_NAME: 17,
                SUMMARY: 19,
                QR: 20
            },
            step: 1,
            prev: [],
            
            registration: {
                type: 'user',  // 'user' eller 'guardian'
                option: 'qr',
                errors: {},
            },
            
            user: {
                phone: null,
                email: null,
                selection: null,
                selected: null,
                primary: false,
                age: 3,
                password: null
            },
            
            guardian: {
                phone: null,
                email: null,
                selection: null,
                selected: null
            },
            
            child: {
                phone: null,
                email: null,
                selection: null,
                selected: null
            }
        }
    },
    computed: {
        isAdult() {
            return this.user.age >= 18;
        },
        
        currentComponent() {
            const componentMap = {
                [this.STEPS.PHONE]: 'PhoneComponent',
                [this.STEPS.EMAIL]: 'EmailCheckComponent',
                // ... map alle komponenter
            };
            return componentMap[this.step];
        },
        
        componentProps() {
            return {
                hasErrors: this.registration.errors,
                guardian: this.step >= this.STEPS.GUARDIAN_PHONE,
                prev: this.prev,
                selection: this.getCurrentSelection,
                selected: this.getCurrentSelected
            };
        }
    },
    methods: {
        getCurrentSelection() {
            if (this.step <= this.STEPS.AGE) return this.user.selection;
            if (this.step <= this.STEPS.CREATION_SELECT) return this.guardian.selection;
            return this.child.selection;
        },
        
        getCurrentSelected() {
            if (this.step <= this.STEPS.AGE) return this.user.selected;
            if (this.step <= this.STEPS.CREATION_SELECT) return this.guardian.selected;
            return this.child.selected;
        },

        navigateToStep(step) {
            this.prev.push(this.step);
            this.step = step;
        },

        handleSuccess(data, nextStep) {
            this.updateCurrentData(data);
            this.navigateToStep(nextStep);
        },

        updateCurrentData(data) {
            const current = this.step <= this.STEPS.AGE ? 'user' 
                : this.step <= this.STEPS.CREATION_SELECT ? 'guardian' 
                : 'child';
            
            Object.assign(this[current], data);
        },

        reset() {
            this.step = this.STEPS.PHONE;
            this.prev = [];
            this.user = { phone: null, email: null, selection: null, selected: null, primary: false, age: 3 };
            this.guardian = { phone: null, email: null, selection: null, selected: null };
            this.child = { phone: null, email: null, selection: null, selected: null };
            this.registration.errors = {};
        }
    }
};
</script>

