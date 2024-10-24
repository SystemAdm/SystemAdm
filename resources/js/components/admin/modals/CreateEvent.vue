<template>
    <div v-if="modelValue" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full px-4">
        <div class="relative top-40 mx-auto shadow-xl rounded-md bg-white max-w-lg">
            <div class="flex justify-end p-2">
                <button @click="closeModal" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-3xl p-1.5 ml-auto inline-flex items-center">
                    &times;
                </button>
            </div>
            <div class="p-6 pt-0 text-center">
                <h1 class="text-2xl font-extrabold text-black">Create new event</h1>
                <form class=" px-8 pt-6 pb-8 mb-4">
                    <div v-if="site === 0">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="event_begin">
                                Event begin
                            </label>
                            <!-- INPUT EVENT BEGIN -->
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="event_begin"
                                type="datetime-local"
                                placeholder="Event begin"
                                v-model="this.event.begin">
                        </div>
                        <div class="">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="event_end">
                                Event end
                            </label>
                            <!-- INPUT EVENT END -->
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="event_end"
                                type="datetime-local"
                                placeholder="Event end"
                                v-model="this.event.end">
                        </div>
                    </div>
                    <div v-if="site === 1" class="text-left">
                        <h3 class="text-black text-xl mb-5">Reservation / Registration needed?</h3>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO REGISTRATION -->
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="registration"
                                   id="registration_needed"
                                   v-model="this.event.registration"
                                   :value="1">
                            <span class="text-sm">Registration needed</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO REGISTRATION -->
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="registration"
                                   id="registration_optional"
                                   v-model="this.event.registration"
                                   :value="2">
                            <span class="text-sm">Optional</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO REGISTRATION -->
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="registration"
                                   id="registration_none"
                                   v-model="this.event.registration"
                                   :value="0">
                            <span class="text-sm">Not needed</span>
                        </label>
                    </div>
                    <div v-if="site === 2" class="text-left">
                        <h3 class="text-black text-xl mb-5">Registration begin</h3>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO REGISTRATION BEGIN -->
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="registration_begin"
                                   id="registration_begin_now"
                                   v-model="this.registration.begin"
                                   :value="1">
                            <span class="text-sm">NOW</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO REGISTRATION BEGIN -->
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="registration_begin"
                                   id="registration_begin_event_begin"
                                   v-model="this.registration.begin"
                                   :value="2">
                            <span class="text-sm">At event beginning</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO REGISTRATION BEGIN -->
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="registration_begin"
                                   id="registration_begin_defined"
                                   v-model="this.registration.begin"
                                   :value="0">
                            <span class="text-sm">Defined</span>
                        </label>
                        <div class="my-4" v-if="this.registration.begin === 0">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="registration_begin">
                                Registration begin
                            </label>
                            <!-- INPUT RADIO REGISTRATION BEGIN DATE -->
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="registration_begin"
                                type="datetime-local"
                                placeholder="Registration begin"
                                v-model="this.registration.begin_date">
                        </div>
                    </div>
                    <div v-if="site === 3" class="text-left">
                        <h3 class="text-black text-xl mb-5">Registration end</h3>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO REGISTRATION END -->
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="registration_end"
                                   id="registration_end_event_begin"
                                   v-model="this.registration.end"
                                   :value="1">
                            <span class="text-sm">At event beginning</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO REGISTRATION END -->
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="registration_end"
                                   id="registration_end_event_end"
                                   v-model="this.registration.end"
                                   :value="2">
                            <span class="text-sm">At event end</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO REGISTRATION END -->
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="registration_end"
                                   id="registration_end_defined"
                                   v-model="this.registration.end"
                                   :value="0">
                            <span class="text-sm">Defined</span>
                        </label>
                        <div class="my-4" v-if="this.registration.end === 0">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="registration_end">
                                Registration end
                            </label>
                            <!-- INPUT RADIO REGISTRATION END DATE -->
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="registration_end"
                                type="datetime-local"
                                placeholder="Registration end"
                                v-model="this.registration.end_date">
                        </div>
                    </div>
                    <div v-if="site === 4" class="text-left">
                        <h3 class="text-black text-xl mb-5">Attending</h3>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO ATTENDING -->
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="attending"
                                   id="attending_all"
                                   v-model="this.attending"
                                   :value="0">
                            <span class="text-sm">Everyone</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO ATTENDING -->
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="attending"
                                   id="attending_members"
                                   v-model="this.attending"
                                   :value="1">
                            <span class="text-sm">Members</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO ATTENDING -->
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="attending"
                                   id="attending_crew"
                                   v-model="this.attending"
                                   :value="2">
                            <span class="text-sm">Crew</span>
                        </label>
                    </div>
                    <div v-if="site === 5" class="text-left">
                        <h3 class="text-black text-xl mb-5">Restriction</h3>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO RESTRICTION -->
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="restriction_type"
                                   id="restriction_type_age"
                                   v-model="this.restriction.type"
                                   value="age">
                            <span class="text-sm">Age</span>
                        </label>
                        <div v-if="restriction.type === 'age'">
                            <label class="text-black">Minimum age: {{ restriction.min.age }} years old</label>
                            <!-- INPUT RANGE RESTRICTION MIN AGE -->
                            <input id="restriction_min_age"
                                   type="range"
                                   v-model="restriction.min.age"
                                   class="block w-full py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:border-blue-500 focus:outline-none focus:ring">
                            <label class="md:w-2/3 block text-gray-500 font-bold">
                                <!-- INPUT CHECK RESTRICTION MAX AGE -->
                                <input class="mr-2 leading-tight"
                                       type="checkbox"
                                       name="restriction_type_age"
                                       id="restriction_age"
                                       v-model="restriction.age"
                                       :value="true">
                                <span class="text-sm">Maximal age?</span>
                            </label>
                            <div v-if="restriction.age">
                                <label class="text-black">Maximum age: {{ restriction.max.age }} years old</label>
                                <!-- INPUT RANGE RESTRICTION MAX AGE -->
                                <input id="restriction_max_age"
                                       type="range"
                                       v-model="restriction.max.age"
                                       class="block w-full py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:border-blue-500 focus:outline-none focus:ring">
                            </div>
                        </div>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO RESTRICTION -->
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="restriction_type"
                                   id="restriction_type_grade"
                                   v-model="this.restriction.type"
                                   value="grade">
                            <span class="text-sm">Grade</span>
                        </label>
                        <div v-if="restriction.type === 'grade'">
                            <label class="text-black">Minimum grade: {{ restriction.min.grade }}. class</label>
                            <!-- INPUT RANGE RESTRICTION MIN GRADE -->
                            <input id="restriction_min_grade"
                                   type="range"
                                   v-model="restriction.min.grade"
                                   class="block w-full py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:border-blue-500 focus:outline-none focus:ring"
                                   min="5"
                                   max="10">
                            <label class="md:w-2/3 block text-gray-500 font-bold">
                                <!-- INPUT CHECK RESTRICTION MAX GRADE -->
                                <input class="mr-2 leading-tight"
                                       type="checkbox"
                                       name="restriction_type"
                                       id="restriction_type_grade"
                                       v-model="restriction.grade"
                                       :value="true">
                                <span class="text-sm">Maximal grade?</span>
                            </label>
                            <div v-if="restriction.grade">
                                <label class="text-black">Maximum grade: {{ restriction.max.grade }}. class</label>
                                <!-- INPUT RANGE RESTRICTION MAX GRADE -->
                                <input id="restriction_max_grade"
                                       type="range"
                                       v-model="restriction.max.grade"
                                       class="block w-full py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:border-blue-500 focus:outline-none focus:ring"
                                       min="5"
                                       max="10">
                            </div>
                        </div>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO RESTRICTION -->
                            <input class="mr-2 leading-tight" type="radio" name="restriction"
                                   id="attending_crew" v-model="this.restriction.type" value="pegi">
                            <span class="text-sm"><abbr title="The Pan-European Game Information">PEGI</abbr></span>
                        </label>
                        <div id="pegi" v-if="this.restriction.type === 'pegi'" class="flex justify-between">
                            <label>
                                <!-- INPUT RADIO RESTRICTION PEGI -->
                                <input type="radio" name="test" value="3" checked v-model="this.restriction.pegi">
                                <img src="/images/pegi/age-3.jpg" alt="From 3 year old">
                            </label>
                            <label>
                                <!-- INPUT RADIO RESTRICTION PEGI -->
                                <input type="radio" name="test" value="7" v-model="this.restriction.pegi">
                                <img src="/images/pegi/age-7.jpg" alt="From 7 year old">
                            </label>
                            <label>
                                <!-- INPUT RADIO RESTRICTION PEGI -->
                                <input type="radio" name="test" value="12" v-model="this.restriction.pegi">
                                <img src="/images/pegi/age-12.jpg" alt="From 12 year old">
                            </label>
                            <label>
                                <!-- INPUT RADIO RESTRICTION PEGI -->
                                <input type="radio" name="test" value="16" v-model="this.restriction.pegi">
                                <img src="/images/pegi/age-16.jpg" alt="From 16 year old">
                            </label>
                            <label>
                                <!-- INPUT RADIO RESTRICTION PEGI -->
                                <input type="radio" name="test" value="18" v-model="this.restriction.pegi">
                                <img src="/images/pegi/age-18.jpg" alt="From 18 year old">
                            </label>

                        </div>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO RESTRICTION -->
                            <input class="mr-2 leading-tight" type="radio" name="restriction"
                                   id="attending_crew" v-model="this.restriction.type" value="esrb">
                            <span class="text-sm"><abbr title="Entertainment Software Rating Board">ESRB</abbr></span>
                        </label>
                        <div id="esrb" v-if="this.restriction.type === 'esrb'" class="flex justify-between">
                            <label>
                                <!-- INPUT RADIO RESTRICTION ESRB -->
                                <input type="radio" name="test" value="3" checked v-model="restriction.esrb">
                                <img src="/images/esrb/E.svg" alt="From 3 year old">
                            </label>
                            <label>
                                <!-- INPUT RADIO RESTRICTION ESRB -->
                                <input type="radio" name="test" value="10" v-model="restriction.esrb">
                                <img src="/images/esrb/E10plus.svg" alt="From 10 year old">
                            </label>
                            <label>
                                <!-- INPUT RADIO RESTRICTION ESRB -->
                                <input type="radio" name="test" value="13" v-model="restriction.esrb">
                                <img src="/images/esrb/T.svg" alt="From 13 year old">
                            </label>
                            <label>
                                <!-- INPUT RADIO RESTRICTION ESRB -->
                                <input type="radio" name="test" value="17" v-model="restriction.esrb">
                                <img src="/images/esrb/M.svg" alt="From 17 year old">
                            </label>
                            <!-- INPUT RADIO RESTRICTION ESRB -->
                            <label>
                                <input type="radio" name="test" value="18" v-model="restriction.esrb">
                                <img src="/images/esrb/AO.svg" alt="From 18 year old">
                            </label>
                        </div>
                    </div>
                    <div v-if="site === 6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                            Title
                        </label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="title"
                            type="text"
                            placeholder="Title"
                            v-model="title">
                        <label for="exampleFormControlTextarea1"
                               class="mt-3 block text-gray-700 text-sm font-bold mb-2">Description</label>
                        <textarea
                            class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                            id="description"
                            rows="3"
                            placeholder="Description"
                            v-model="description"></textarea>
                    </div>
                    <div v-if="site === 7">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="seats">
                            Number of seats
                            <span class="block text-gray-400">0: No limit, -1: No seats</span>
                        </label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="seats" type="number" placeholder="Number of seats" v-model="seats">
                    </div>
                    <div v-if="site === 8">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                            Location
                        </label>
                        <select
                            class="form-select appearance-none block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding bg-no-repeat border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                            v-model="location">
                            <option v-for="location in locations" :value="location.id">{{ location.name }},
                                {{ location.street_name }} {{ location.street_number }}
                            </option>
                        </select>
                    </div>
                    <div v-if="site === 9">
                        <div id="img" v-for="img in images" class="inline-flex">
                            <label class="flex justify-between">
                                <input type="radio" name="test" :value="img" v-model="this.image">
                                <img :src="img" :alt="img">
                            </label>

                        </div>
                        <div class="block text-gray-700 text-sm font-bold mb-2">
                            <input type="file" placeholder="Upload image"
                                   class="text-black shadow border border-double border-gray-300 rounded-md"
                                   v-on:change="upload">
                        </div>
                    </div>
                </form>
                <button v-if="site >= 9" @click="next"
                        class="text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center mr-2">
                    Submit
                </button>
                <button v-else @click="next"
                        class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center mr-2">
                    Next
                </button>
                <button v-if="site !== 0" @click="prev"
                        class="text-white bg-yellow-600 hover:bg-yellow-800 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center mr-2">
                    Prev
                </button>
                <button @click="closeModal"
                        class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center">
                    Cancel
                </button>
                <span class="text-black">{{ this.site }}</span>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import {DateTime} from "luxon";

export default {
    props: {
        modelValue: {
            type: Boolean,
            required: true
        }
    },
    data() {
        return {
            locations: [],
            site: 0,
            event: {
                begin: null, // Datetime
                end: null,   // Datetime
                registration: null, // 0,1,2
            },
            registration: {
                begin: null, // 0,1,2
                begin_date: null, // Datetime
                end: null, // 0,1,2
                end_date: null // Datetime
            },
            attending: null,
            image: null,
            location: 1,
            title: null,
            description: null,
            seats: 0,
            images: [],
            upload: null,
            restriction: {
                type: null,
                age: false,
                grade: false,
                pegi: 3,
                esrb: 3,
                min: {
                    age: 13,
                    grade: 8,
                },
                max: {
                    age: 13,
                    grade: 8,
                }
            },
        }
    },
    methods: {
        next() {
            // Image
            if (this.site === 9) {
                axios.post('/api/admin/events', {}).then(response => {
                })
            }
            // Location
            if (this.site === 8) {
                this.site++;
            }
            // Seats
            if (this.site === 7) {
                this.site++;
            }
            // Title & Description
            if (this.site === 6) {
                this.site++;
            }
            // Restriction
            if (this.site === 5) {
                this.site++;
            }
            // Attending
            if (this.site === 4) {
                this.site++;
            }
            // Reservation end
            if (this.site === 3) {
                if (this.registration.end === 0 && (this.registration.end_date === null || this.registration.end_date === '' || this.registration.end_date > this.event.end)) {
                    return;
                }
                this.site++;
            }
            // Reservation begin
            if (this.site === 2) {
                if (this.registration.begin === 0 && (this.registration.begin_date === null || this.registration.begin_date === '' || this.registration.begin_date > this.event.begin)) {
                    return;
                }
                this.site++;
            }
            // Reservation
            if (this.site === 1) {
                if (this.event.registration === 0) {
                    this.site = this.site + 2;
                }
                this.site++;
            }
            // Event beginning and end
            if (this.site === 0) {
                console.log(this.event.begin > DateTime.now().toISO());
                console.log(this.event.end > this.event.begin)
                if ((this.event.end > this.event.begin) && (this.event.begin > DateTime.now().toISO())) {
                    console.log('b')
                    this.site++;
                }
            }

        },
        prev() {
            if (this.site === 1) {
                this.site--;
            }
            if (this.site === 2) {
                this.site--;
            }
            if (this.site === 3) {
                if (this.event.registration === 0) {
                    this.site--;
                }
                this.site--;
            }
            if (this.site === 4) {
                if (this.event.registration === 0) {
                    this.site = this.site - 2;
                }
                this.site--;
            }
            if (this.site === 5) {
                this.site--;
            }
            if (this.site === 6) {
                this.site--;
            }
            if (this.site === 7) {
                this.site--;
            }
            if (this.site === 8) {
                this.site--;
            }
            if (this.site === 9) {
                this.site--;
            }
        },
        closeModal() {
            this.$emit('update:modelValue', false); // Emit the event to update parent state
        },
        confirmAction() {
            // Handle your confirmation logic here
            this.closeModal();
            console.log('Action confirmed.');
        },
        fetch() {
            axios.get("/api/locations").then(response => {
                this.locations = response.data;
            });
            axios.get('/api/events/images').then(response => {
                this.images = response.data;
            })
        }
    },
    mounted() {
        this.fetch();
    }
};
</script>
<style scoped>
/* HIDE RADIO */
#esrb [type=radio], #pegi [type=radio], #img [type=radio] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

/* IMAGE STYLES */
#esrb [type=radio] + img, #pegi [type=radio] + img, #img [type=radio] + img {
    cursor: pointer;
    height: 50px;
}

/* CHECKED STYLES */
#esrb [type=radio]:checked + img, #pegi [type=radio]:checked + img, #img [type=radio]:checked + img {
    outline: 2px solid #00f;
}
</style>
