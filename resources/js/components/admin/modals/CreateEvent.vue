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
                <h1 class="text-2xl font-extrabold text-black">Create new event</h1>
                <form class=" px-8 pt-6 pb-8 mb-4">
                    <div v-if="step === 0">
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
                                v-model="event.begin"
                                @focus="error.event.begin = false"
                                :class="{'ring ring-red-300': error.event.begin}">
                            <div v-if="error.event.begin" class="text-red-400 pt-2">{{ error.event.begin }}</div>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="event_end">
                                Event end
                            </label>
                            <!-- INPUT EVENT END -->
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="event_end"
                                type="datetime-local"
                                placeholder="Event end"
                                v-model="event.end"
                                @focus="error.event.end = false"
                                :class="{'ring ring-red-300': error.event.end}">
                            <div v-if="error.event.end" class="text-red-400 pt-2">{{ error.event.end }}</div>
                        </div>
                    </div>
                    <div v-if="step === 1" class="text-left">
                        <h3 class="text-black text-xl mb-5">Reservation / Registration needed?</h3>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="registration"
                                   id="registration_needed"
                                   v-model="event.registration"
                                   :value="1">
                            <span class="text-sm">Registration needed</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="registration"
                                   id="registration_optional"
                                   v-model="event.registration"
                                   :value="2">
                            <span class="text-sm">Optional</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="registration"
                                   id="registration_none"
                                   v-model="event.registration"
                                   :value="0">
                            <span class="text-sm">Not needed</span>
                        </label>
                    </div>
                    <div v-if="step === 2" class="text-left">
                        <h3 class="text-black text-xl mb-5">Registration begin</h3>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO REGISTRATION BEGIN -->
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="registration_begin"
                                   id="registration_begin_now"
                                   v-model="registration.begin"
                                   :value="1">
                            <span class="text-sm">NOW</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO REGISTRATION BEGIN -->
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="registration_begin"
                                   id="registration_begin_event_begin"
                                   v-model="registration.begin"
                                   :value="2">
                            <span class="text-sm">At event beginning</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO REGISTRATION BEGIN -->
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="registration_begin"
                                   id="registration_begin_defined"
                                   v-model="registration.begin"
                                   :value="0">
                            <span class="text-sm">Defined</span>
                        </label>
                        <div class="my-4" v-if="registration.begin === 0">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="registration_begin">
                                Registration begin
                            </label>
                            <!-- INPUT RADIO REGISTRATION BEGIN DATE -->
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="registration_begin"
                                type="datetime-local"
                                placeholder="Registration begin"
                                v-model="registration.begin_date"
                                @focus="error.registration.begin = false"
                                :class="{'ring ring-red-300': error.registration.begin}"
                            >
                            <div v-if="error.registration.begin" class="text-red-400 pt-2">{{
                                    error.registration.begin
                                }}
                            </div>
                        </div>
                    </div>
                    <div v-if="step === 3" class="text-left">
                        <h3 class="text-black text-xl mb-5">Registration end</h3>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO REGISTRATION END -->
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="registration_end"
                                   id="registration_end_event_begin"
                                   v-model="registration.end"
                                   :value="1">
                            <span class="text-sm">At event beginning</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO REGISTRATION END -->
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="registration_end"
                                   id="registration_end_event_end"
                                   v-model="registration.end"
                                   :value="2">
                            <span class="text-sm">At event end</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <!-- INPUT RADIO REGISTRATION END -->
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="registration_end"
                                   id="registration_end_defined"
                                   v-model="registration.end"
                                   :value="0">
                            <span class="text-sm">Defined</span>
                        </label>
                        <div class="my-4" v-if="registration.end === 0">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="registration_end">
                                Registration end
                            </label>
                            <!-- INPUT RADIO REGISTRATION END DATE -->
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="registration_end"
                                type="datetime-local"
                                placeholder="Registration end"
                                v-model="registration.end_date"
                                @focus="error.registration.end = false"
                                :class="{'ring ring-red-300': error.registration.end}"
                            >
                            <div v-if="error.registration.end" class="text-red-400 pt-2">{{
                                    error.registration.end
                                }}
                            </div>
                        </div>
                    </div>
                    <div v-if="step === 4" class="text-left">
                        <h3 class="text-black text-xl mb-5">Attending</h3>

                        <label class="md:w-2/3 block text-gray-500 font-bold" for="attending_all">
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="attending"
                                   id="attending_all"
                                   v-model="attending"
                                   :value="0">
                            <span class="text-sm">Everyone</span>
                        </label>

                        <label class="md:w-2/3 block text-gray-500 font-bold" for="attending_members">
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="attending"
                                   id="attending_members"
                                   v-model="attending"
                                   :value="1">
                            <span class="text-sm">Members</span>
                        </label>

                        <label class="md:w-2/3 block text-gray-500 font-bold" for="attending_crew">
                            <input class="mr-2 leading-tight"
                                   type="radio"
                                   name="attending"
                                   id="attending_crew"
                                   v-model="attending"
                                   :value="2">
                            <span class="text-sm">Crew</span>
                        </label>
                    </div>
                    <div v-if="step === 5" class="text-left">
                        <h3 class="text-black text-xl mb-5">Restriction</h3>
                        <!-- Restriction Types Radio Buttons -->
                        <div v-for="option in restrictionOptions" :key="option.value"
                             class="md:w-2/3 block text-gray-500 font-bold">
                            <label class="flex items-center">
                                <input
                                    class="mr-2 leading-tight text-black"
                                    type="radio"
                                    name="restriction_type"
                                    :id="`restriction_type_${option.value}`"
                                    v-model="restriction.type"
                                    :value="option.value"
                                >
                                <span class="text-sm">{{ option.label }}</span>
                            </label>
                        </div>

                        <!-- Age Restriction Section -->
                        <div v-if="restriction.type === 'age'">
                            <label class="text-black">Minimum age: {{ restriction.min.age }} years old</label>
                            <input
                                id="restriction_min_age"
                                type="range"
                                v-model="restriction.min.age"
                                class="block w-full py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:border-blue-500 focus:outline-none focus:ring"
                            >
                            <label class="md:w-2/3 block text-gray-500 font-bold">
                                <input
                                    class="mr-2 leading-tight"
                                    type="checkbox"
                                    name="restriction_max_age"
                                    id="restriction_age"
                                    v-model="restriction.limit.age"
                                    :value="true"
                                >
                                <span class="text-sm">Maximal age?</span>
                            </label>
                            <div v-if="restriction.limit.age">
                                <label class="text-black">Maximum age: {{ restriction.max.age }} years old</label>
                                <input
                                    id="restriction_max_age"
                                    type="range"
                                    v-model="restriction.max.age"
                                    class="block w-full py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:border-blue-500 focus:outline-none focus:ring"
                                >
                            </div>
                        </div>

                        <!-- Grade Restriction Section -->
                        <div v-if="restriction.type === 'grade'">
                            <label class="text-black">Minimum grade: {{ restriction.min.grade }} class</label>
                            <input
                                id="restriction_min_grade"
                                type="range"
                                v-model="restriction.min.grade"
                                class="block w-full py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:border-blue-500 focus:outline-none focus:ring"
                                min="5"
                                max="10"
                            >
                            <label class="md:w-2/3 block text-gray-500 font-bold">
                                <input
                                    class="mr-2 leading-tight"
                                    type="checkbox"
                                    name="restriction_max_grade"
                                    id="restriction_max_grade"
                                    v-model="restriction.limit.grade"
                                    :value="true"
                                >
                                <span class="text-sm">Maximal grade?</span>
                            </label>
                            <div v-if="restriction.limit.grade">
                                <label class="text-black">Maximum grade: {{ restriction.max.grade }} class</label>
                                <input
                                    id="restriction_max_grade"
                                    type="range"
                                    v-model="restriction.max.grade"
                                    class="block w-full py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:border-blue-500 focus:outline-none focus:ring"
                                    min="5"
                                    max="10"
                                >
                            </div>
                        </div>
                        <hr class="border-gray-600" size="30"/>
                        <!-- PEGI Restriction Section -->
                        <div v-if="restriction.type === 'pegi'" class="flex justify-between">
                            <template v-for="(label, value) in pegiOptions" :key="value">
                                <label>
                                    <input type="radio" :name="`pegi_${value}`" :value="value"
                                           v-model="restriction.pegi">
                                    <img :src="`/images/pegi/age-${value}.jpg`" :alt="`From ${value} years old`">
                                </label>
                            </template>
                        </div>

                        <!-- ESRB Restriction Section -->
                        <div v-if="restriction.type === 'esrb'" class="flex justify-between">
                            <template v-for="(src, key) in esrb" :key="key">
                                <label>
                                    <input type="radio" :name="`esrb_${key}`" :value="key" v-model="restriction.esrb">
                                    <img :src="`/images/esrb/${src}`" :alt="`From ${key} years old`">
                                </label>
                            </template>
                        </div>
                    </div>
                    <div v-if="step === 6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                            Title
                        </label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="title"
                            type="text"
                            placeholder="Title"
                            v-model="title"
                            @focus="error.title = false"
                            :class="{'ring ring-red-300':error.title}"
                        >
                        <div v-if="error.title" class="text-red-400 pt-2">{{ error.title }}</div>
                        <label for="exampleFormControlTextarea1"
                               class="mt-3 block text-gray-700 text-sm font-bold mb-2">Description</label>
                        <textarea
                            class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                            id="description"
                            rows="3"
                            placeholder="Description"
                            v-model="description"
                            @focus="error.description = false"
                            :class="{'ring ring-red-300':error.description}"
                        ></textarea>
                        <div v-if="error.description" class="text-red-400 pt-2">{{ error.description }}</div>
                    </div>
                    <div v-if="step === 7">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="seats">
                            Number of seats
                            <span class="block text-gray-400">0: No limit, -1: No seats</span>
                        </label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="seats"
                            type="number"
                            placeholder="Number of seats"
                            v-model.number="seats"
                            @input="validateSeats"
                        >
                    </div>
                    <div v-if="step === 8">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                            Location
                        </label>
                        <select
                            class="form-select appearance-none block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding bg-no-repeat border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                            v-model="selectedLocation"
                        >
                            <option v-for="location in locations" :key="location.id" :value="location.id">
                                {{ location.name }},
                                {{ location.street_name }} {{ location.street_number }}
                            </option>
                        </select>
                    </div>
                    <div v-if="step === 9">
                        <div class="grid grid-cols-5 gap-4">
                        <div id="img" v-for="img in images" :key="img" class=" items-center">
                            <label class=" items-center">
                                <input type="radio" name="selected_image" :value="img.name" v-model="image" class="sr-only">
                                <img :src="img.url" :alt="img.name" class="h-20 w-20 object-cover rounded-md shadow">
                                <span class="mt-1 text-sm text-center">{{ img.url.split('/').pop() }}</span>
                                <!-- Display image name -->
                            </label>
                        </div>
                        </div>
                        <div class="block text-gray-700 text-sm font-bold mb-2">
                            <input type="file" placeholder="Upload image"
                                   class="text-black shadow border border-double border-gray-300 rounded-md"
                                   v-on:change="upload">
                        </div>
                    </div>
                </form>
                <button v-if="step >= 9" @click="confirmAction"
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
                <button @click="closeModal"
                        class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center">
                    Cancel
                </button>
                <span class="text-black">{{ step }}</span>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import {DateTime} from "luxon";

export default {
    props: {
        modalOpen: {
            type: Boolean,
            required: true
        }
    },
    data() {
        return {
            locations: [],
            step: 9,
            error: {
                event: {
                    begin: false,
                    end: false,
                },
                registration: {
                    begin: false,
                    end: false,
                },
                title: false,
                description: false,
            },
            event: {
                begin: null, // Datetime
                end: null,   // Datetime
                registration: 2, // 0,1,2
            },
            registration: {
                begin: 1, // 0,1,2
                begin_date: null, // Datetime
                end: 2, // 0,1,2
                end_date: null // Datetime
            },
            attending: 0,
            image: null,
            selectedLocation: 1,
            title: null,
            description: null,
            seats: 0,
            images: [],
            restriction: {
                type: 'age',
                min: {age: 13, grade: 5},
                max: {age: 25, grade: 10},
                limit: {age: false, grade: false},
                pegi: 3,
                esrb: 3,
            },
            esrb: {3: 'E.svg', 10: 'E10plus.svg', 13: 'T.svg', 17: 'M.svg', 18: 'AO.svg'},
            pegiOptions: {3: 'From 3', 7: 'From 7', 12: 'From 12', 16: 'From 16', 18: 'From 18'},
            restrictionOptions: [
                {value: 'age', label: 'Age'},
                {value: 'grade', label: 'Grade'},
                {value: 'pegi', label: 'PEGI'},
                {value: 'esrb', label: 'ESRB'},
            ],
        }
    },
    emits: ['update:created','update:modalOpen'],
    methods: {
        next() {
                        // Location
            if (this.step === 8) {
                this.step++;
            }
            // Seats
            if (this.step === 7) {
                this.step++;
            }
            // Title & Description
            if (this.step === 6) {
                if (this.title == null) {
                    this.error.title = 'Title must have a value';
                } else if (this.title.length <= 3) {
                    this.error.title = 'Title is too short, min 3 characters';
                } else if (this.title.length >= 64) {
                    this.error.title = 'Title must be shorter, max 64 characters';
                }

                if (this.description == null) {
                    this.error.description = 'There must be a description';
                } else if (this.description.length <= 10) {
                    this.error.description = 'Description is too short, min 10 characters';
                } else if (this.description.length >= 2048) {
                    this.error.description = 'Description is too long, max 2048 characters';
                }

                if (!this.error.title && !this.error.description) {
                    this.error.description = false;
                    this.error.title = false;
                    this.step++;
                }
            }
            // Restriction
            if (this.step === 5) {
                this.step++;
            }
            // Attending
            if (this.step === 4) {
                this.step++;
            }
            // Reservation end
            if (this.step === 3) {
                if (this.registration.end === 0 && !this.registration.end_date) {
                    this.error.registration.end = 'Date must be defined';
                } else if (this.registration.end === 0 && this.registration.end_date < this.registration.begin_date) {
                    this.error.registration.end = 'Date must be after the registration has begun';
                } else if (this.registration.end === 0 && this.registration.end_date > this.event.end_date) {
                    this.error.registration.end = 'Date must be before the end of the event';
                } else {
                    this.error.registration.end = false;  // Clear any previous errors
                    this.step++;
                }
            }
            // Reservation begin
            if (this.step === 2) {
                if (this.registration.begin === 0 && !this.registration.begin_date) {
                    this.error.registration.begin = 'Date must be defined';
                } else if (this.registration.begin === 0 && this.registration.begin_date < this.event.begin_date) {
                    this.error.registration.begin = 'Date must be before the event beginning';
                } else {
                    this.error.registration.begin = false; // Clear any previous errors
                    this.step++;
                }
            }
            // Reservation
            if (this.step === 1) {
                if (this.event.registration === 0) {
                    this.step = this.step + 2;
                }
                this.step++;
            }
            // Event beginning and end
            if (this.step === 0) {
                const now = DateTime.now().toISO();
                const beginDate = DateTime.fromISO(this.event.begin);
                const endDate = DateTime.fromISO(this.event.end);

                if (!this.event.begin) {
                    this.error.event.begin = 'Begin field must be filled';
                } else if (beginDate < now) {
                    this.error.event.begin = 'The event cannot have started already.';
                }

                if (!this.event.end) {
                    this.error.event.end = 'End field must be filled';
                } else if (endDate < beginDate) {
                    this.error.event.end = 'End of event must be after event beginning.';
                }

                if (!this.error.event.begin && !this.error.event.end) {
                    this.step++;
                }
            }

        },
        prev() {
            if (this.step === 1) {
                this.step--;
            }
            if (this.step === 2) {
                this.step--;
            }
            if (this.step === 3) {
                if (this.event.registration === 0) {
                    this.step--;
                }
                this.step--;
            }
            if (this.step === 4) {
                if (this.event.registration === 0) {
                    this.step = this.step - 2;
                }
                this.step--;
            }
            if (this.step === 5) {
                this.step--;
            }
            if (this.step === 6) {
                this.step--;
            }
            if (this.step === 7) {
                this.step--;
            }
            if (this.step === 8) {
                this.step--;
            }
            if (this.step === 9) {
                this.step--;
            }
        },
        closeModal() {
            console.log("closeModal");
            this.$emit('update:modalOpen', false); // Emit the event to update parent state
        },
        confirmAction() {
            if (this.step === 9) {
                axios.post('/api/admin/events/',{
                    event: this.event,
                    registration: this.registration,
                    attending: this.attending,
                    image: this.image,
                    location:this.selectedLocation,
                    title:this.title,
                    description:this.description,
                    seats:this.seats,
                    restriction:this.restriction,
                }).then(response => {
                    this.$emit('update:created');
                    this.closeModal();
                })
            }
            // Handle your confirmation logic here
            //this.closeModal();
            console.log('Action confirmed.');
        },
        fetchLocations(){
            axios.get("/api/locations").then(response => {
                this.locations = response.data;
            });
        },
        fetchImages(){
            axios.get('/api/events/images').then(response => {
                this.images = response.data;
            });
        },
        fetch() {
            this.fetchLocations();
            this.fetchImages();
        },
        upload(event) {
            const file = event.target.files[0];
            if (file) {
                const formData = new FormData();
                formData.append('file', file);
                axios.post('/api/admin/upload/events', formData,{
                    headers:{
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(response => {
                    this.fetchImages();
                }).catch(error => {console.log(error)});
                // Handle the file upload logic here
                console.log('File uploaded:', file);
                // For example, you could upload the file to a server or update the images array
            }
        },
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
