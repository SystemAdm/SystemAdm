<!-- Signup.vue -->
<template>
    <div class="fixed z-50 inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full px-4 text-black">
        <div class="m-5 pt-5 relative mx-auto shadow-xl rounded-md bg-white max-w-lg">
            <div class="p-6 pt-0 text-center">
                <h1 class="text-2xl font-extrabold text-black">Opprett ny bruker</h1>
                
                <!-- Debug info -->
                <div class="text-sm text-gray-500 mb-4">
                    Nåværende steg: {{ step }}
                    <br>
                    Komponent: {{ currentComponent }}
                </div>

                <div class="px-8 pt-6">
                    <component 
                        v-if="currentComponent"
                        :is="currentComponent"
                        v-bind="componentProps"
                        :hasErrors="registration.errors"
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
        QrCode,
        PhoneComponent,
        SelectNameComponent,
        NameComponent,
        SelectCreationComponent,
        SelectAgeComponent,
        EmailComponent,
        PasswordComponent,
        PasswordCheckComponent,
        FamilyPhoneComponent,
        FamilyNameComponent,
        SummaryAgreement,
        EmailCheckComponent,
        FontAwesomeIcon
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
                type: 'user',
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
            switch(this.step) {
                case 1: return PhoneComponent;
                case 2: return EmailCheckComponent;
                case 3: return SelectNameComponent;
                case 4: return PasswordCheckComponent;
                case 5: return NameComponent;
                case 6: return SelectAgeComponent;
                case 7: return FamilyPhoneComponent;
                case 8: return EmailComponent;
                case 9: return SelectNameComponent;
                case 10: return FamilyNameComponent;
                case 11: return SelectCreationComponent;
                case 12: return EmailComponent;
                case 13: return PasswordComponent;
                case 14: return FamilyPhoneComponent;
                case 15: return EmailComponent;
                case 16: return SelectNameComponent;
                case 17: return FamilyNameComponent;
                case 19: return SummaryAgreement;
                case 20: return 'QrCode';
                default: return null;
            }
        },
        componentProps() {
            // Base props som alle komponenter trenger
            const baseProps = {
                hasErrors: this.registration.errors,
                prev: this.prev,
                STEPS: this.STEPS
            };

            // Spesifikke props basert på gjeldende steg
            if (this.step === this.STEPS.QR) {
                return {
                    ...baseProps,
                    user: this.user
                };
            }

            switch (this.step) {
                case this.STEPS.PASSWORD_CHECK:
                    return {
                        ...baseProps,
                        selected: this.user
                    };
                case this.STEPS.CREATION_SELECT:
                    return {
                        ...baseProps
                    };
                default:
                    return {
                        ...baseProps,
                        guardian: this.step >= this.STEPS.GUARDIAN_PHONE,
                        selection: this.user.selection,
                        selected: this.user.selected,
                        phone: this.user.phone,
                        email: this.user.email,
                        password: this.user.password,
                        age: this.user.age,
                        type: this.registration.type,
                        option: this.registration.option,
                        user: this.user,
                        guardianData: this.guardian,
                        childData: this.child
                    };
            }
        }
    },
    methods: {
        getCurrentObject() {
            if (this.step <= this.STEPS.AGE) return this.user;
            if (this.step <= this.STEPS.CREATION_SELECT) return this.guardian;
            return this.child;
        },
        
        navigateToStep(step) {
            console.log('Navigating to step:', step); // Debug
            if (!step) {
                console.error('Invalid step provided:', step);
                return;
            }
            this.prev.push(this.step);
            this.step = step;
        },

        handleSuccess(data, nextStep) {
            console.log('Handle success:', data, 'Next step:', nextStep);
            
            if (data) {
                if (data.selection) {
                    // Lagre utvalget av brukere og telefonnummer
                    this.user.selection = data.selection;
                    this.user.phone = data.phone;
                } else if (data.selected) {
                    // Bruker har valgt en eksisterende konto - rens objektet
                    const {
                        id, given_name, family_name, display_name, 
                        email, phone, profile
                    } = data.selected;
                    
                    // Oppdater user med bare de nødvendige feltene
                    this.user = {
                        id,
                        given_name,
                        family_name,
                        display_name,
                        email,
                        phone: data.phone || phone,
                        profile
                    };
                } else {
                    // Oppdater andre data
                    Object.assign(this.user, data);
                }
            }

            // Oppdater steg
            if (nextStep) {
                this.prev.push(this.step);
                this.step = nextStep;
                console.log('Updated step:', this.step, 'Current user:', this.user);
            }
        },

        reset() {
            this.step = this.STEPS.PHONE;
            this.prev = [];
            this.user = { 
                phone: null, 
                email: null, 
                selection: null, 
                selected: null, 
                primary: false, 
                age: 3, 
                password: null 
            };
            this.guardian = { 
                phone: null, 
                email: null, 
                selection: null, 
                selected: null 
            };
            this.child = { 
                phone: null, 
                email: null, 
                selection: null, 
                selected: null 
            };
            this.registration.errors = {};
            this.$emit('close');
        }
    }
}
</script>

