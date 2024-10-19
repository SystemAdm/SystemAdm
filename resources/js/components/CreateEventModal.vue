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
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="event_begin" type="datetime-local" placeholder="Event begin"
                                v-model="this.event.begin">
                        </div>
                        <div class="">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="event_end">
                                Event end
                            </label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="event_end" type="datetime-local" placeholder="Event end" v-model="this.event.end">
                        </div>
                    </div>
                    <div v-if="site === 1" class="text-left">
                        <h3 class="text-black text-xl mb-5">Reservation / Registration needed?</h3>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <input class="mr-2 leading-tight" type="radio" name="registration" id="registration_needed"
                                   v-model="this.event.registration" :value="1">
                            <span class="text-sm">Registration needed</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <input class="mr-2 leading-tight" type="radio" name="registration"
                                   id="registration_optional" v-model="this.event.registration" :value="2">
                            <span class="text-sm">Optional</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <input class="mr-2 leading-tight" type="radio" name="registration" id="registration_none"
                                   v-model="this.event.registration" :value="0">
                            <span class="text-sm">Not needed</span>
                        </label>
                    </div>
                    <div v-if="site === 2" class="text-left">
                        <h3 class="text-black text-xl mb-5">Registration begin</h3>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <input class="mr-2 leading-tight" type="radio" name="registration_begin"
                                   id="registration_end_now" v-model="this.registration.begin" :value="1">
                            <span class="text-sm">NOW</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <input class="mr-2 leading-tight" type="radio" name="registration_begin"
                                   id="registration_end_beginning" v-model="this.registration.begin" :value="2">
                            <span class="text-sm">At event beginning</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <input class="mr-2 leading-tight" type="radio" name="registration_begin"
                                   id="registration_end_defined" v-model="this.registration.begin" :value="0">
                            <span class="text-sm">Defined</span>
                        </label>
                        <div class="my-4" v-if="this.registration.begin === 0">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="registration_begin">
                                Registration begin
                            </label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="registration_begin" type="datetime-local" placeholder="Registration begin"
                                v-model="this.registration.begin_date">
                        </div>
                    </div>
                    <div v-if="site === 3" class="text-left">
                        <h3 class="text-black text-xl mb-5">Registration end</h3>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <input class="mr-2 leading-tight" type="radio" name="registration_end"
                                   id="registration_end_beginning" v-model="this.registration.end" :value="1">
                            <span class="text-sm">At event beginning</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <input class="mr-2 leading-tight" type="radio" name="registration_end"
                                   id="registration_end_end" v-model="this.registration.end" :value="2">
                            <span class="text-sm">At event end</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <input class="mr-2 leading-tight" type="radio" name="registration_end"
                                   id="registration_end_defined" v-model="this.registration.end" :value="0">
                            <span class="text-sm">Defined</span>
                        </label>
                        <div class="my-4" v-if="this.registration.end === 0">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="registration_end">
                                Registration end
                            </label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="registration_end" type="datetime-local" placeholder="Registration begin"
                                v-model="this.registration.end_date">
                        </div>
                    </div>
                    <div v-if="site === 4" class="text-left">
                        <h3 class="text-black text-xl mb-5">Attending</h3>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <input class="mr-2 leading-tight" type="radio" name="attending"
                                   id="attending_all" v-model="this.attending" :value="0">
                            <span class="text-sm">Everyone</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <input class="mr-2 leading-tight" type="radio" name="attending"
                                   id="attending_members" v-model="this.attending" :value="1">
                            <span class="text-sm">Members</span>
                        </label>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <input class="mr-2 leading-tight" type="radio" name="attending"
                                   id="attending_crew" v-model="this.attending" :value="2">
                            <span class="text-sm">Crew</span>
                        </label>
                    </div>
                    <div v-if="site === 5" class="text-left">
                        <h3 class="text-black text-xl mb-5">Restriction</h3>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <input class="mr-2 leading-tight" type="radio" name="restriction"
                                   id="attending_all" v-model="this.restriction.type" value="age">
                            <span class="text-sm">Age</span>
                        </label>
                        <div v-if="restriction.type === 'age'">
                            <label class="text-black">Minimum age: {{ restriction.min.age }} years old</label>
                            <input id="range" type="range" v-model="restriction.min.age"
                                   class="block w-full py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:border-blue-500 focus:outline-none focus:ring">
                            <label class="md:w-2/3 block text-gray-500 font-bold">
                                <input class="mr-2 leading-tight" type="checkbox" name="attending"
                                       id="attending_crew" v-model="restriction.age" :value="true">
                                <span class="text-sm">Maximal age?</span>
                            </label>
                            <div v-if="restriction.age">
                                <label class="text-black">Maximum age: {{ restriction.max.age }} years old</label>
                                <input id="range" type="range" v-model="restriction.max.age"
                                       class="block w-full py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:border-blue-500 focus:outline-none focus:ring">
                            </div>
                        </div>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <input class="mr-2 leading-tight" type="radio" name="restriction"
                                   id="attending_members" v-model="this.restriction.type" value="grade">
                            <span class="text-sm">Grade</span>
                        </label>
                        <div v-if="restriction.type === 'grade'">
                            <label class="text-black">Minimum grade: {{ restriction.min.grade }}. class</label>
                            <input id="range" type="range" v-model="restriction.min.grade"
                                   class="block w-full py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:border-blue-500 focus:outline-none focus:ring"
                                   min="5" max="10">
                            <label class="md:w-2/3 block text-gray-500 font-bold">
                                <input class="mr-2 leading-tight" type="checkbox" name="attending"
                                       id="attending_crew" v-model="restriction.grade" :value="true">
                                <span class="text-sm">Maximal grade?</span>
                            </label>
                            <div v-if="restriction.grade">
                                <label class="text-black">Maximum grade: {{ restriction.max.grade }}. class</label>
                                <input id="range" type="range" v-model="restriction.max.grade"
                                       class="block w-full py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:border-blue-500 focus:outline-none focus:ring"
                                       min="5" max="10">
                            </div>
                        </div>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <input class="mr-2 leading-tight" type="radio" name="restriction"
                                   id="attending_crew" v-model="this.restriction.type" value="pegi">
                            <span class="text-sm"><abbr title="The Pan-European Game Information">PEGI</abbr></span>
                        </label>
                        <div id="pegi" v-if="this.restriction.type === 'pegi'" class="flex justify-between">
                            <label>
                                <input type="radio" name="test" value="small" checked>
                                <img src="/images/pegi/age-3.jpg" alt="Option 1">
                            </label>
                            <label>
                                <input type="radio" name="test" value="big">
                                <img src="/images/pegi/age-7.jpg" alt="Option 2">
                            </label>
                            <label>
                                <input type="radio" name="test" value="small">
                                <img src="/images/pegi/age-12.jpg" alt="Option 3">
                            </label>

                            <label>
                                <input type="radio" name="test" value="big">
                                <img src="/images/pegi/age-16.jpg" alt="Option 4">
                            </label>
                            <label>
                                <input type="radio" name="test" value="small">
                                <img src="/images/pegi/age-18.jpg" alt="Option 5">
                            </label>

                        </div>
                        <label class="md:w-2/3 block text-gray-500 font-bold">
                            <input class="mr-2 leading-tight" type="radio" name="restriction"
                                   id="attending_crew" v-model="this.restriction.type" value="esrb">
                            <span class="text-sm"><abbr title="Entertainment Software Rating Board">ESRB</abbr></span>
                        </label>

                        <div id="esrb" v-if="this.restriction.type === 'esrb'" class="flex justify-between">
                            <label>
                                <input type="radio" name="test" value="small" checked>
                                <img src="/images/esrb/E.svg" alt="Option 1">
                            </label>
                            <label>
                                <input type="radio" name="test" value="big">
                                <img src="/images/esrb/E10plus.svg" alt="Option 2">
                            </label>
                            <label>
                                <input type="radio" name="test" value="small">
                                <img src="/images/esrb/T.svg" alt="Option 3">
                            </label>

                            <label>
                                <input type="radio" name="test" value="big">
                                <img src="/images/esrb/M.svg" alt="Option 4">
                            </label>
                            <label>
                                <input type="radio" name="test" value="small">
                                <img src="/images/esrb/AO.svg" alt="Option 5">
                            </label>
                        </div>
                    </div>
                    <div v-if="site === 6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                            Title
                        </label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="username" type="text" placeholder="Title">
                        <label for="exampleFormControlTextarea1" class="mt-3 block text-gray-700 text-sm font-bold mb-2"
                        >Description</label
                        >
                        <textarea
                            class="
        form-control
        block
        w-full
        px-3
        py-1.5
        text-base
        font-normal
        text-gray-700
        bg-white bg-clip-padding
        border border-solid border-gray-300
        rounded
        transition
        ease-in-out
        m-0
        focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none
      "
                            id="exampleFormControlTextarea1"
                            rows="3"
                            placeholder="Description"
                        ></textarea>
                    </div>
                    <div v-if="site === 7">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                            Number of seats
                            <span class="block text-gray-400">0: No limit, -1: No seats</span>
                        </label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="username" type="number" placeholder="Number of seats" v-model="seats">
                    </div>
                    <div v-if="site === 8">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                            Location
                        </label>
                        <select class="form-select appearance-none
      block
      w-full
      px-3
      py-1.5
      text-base
      font-normal
      text-gray-700
      bg-white bg-clip-padding bg-no-repeat
      border border-solid border-gray-300
      rounded
      transition
      ease-in-out
      m-0
      focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" aria-label="Default select example">
                            <option v-for="location in locations" :value="location.id">{{ location.name }},
                                {{ location.street_name }} {{ location.street_number }}
                            </option>
                        </select>
                    </div>
                    <div v-if="site === 9" id="img" v-for="image in images" class="inline-flex">
                        <label class="flex justify-between">
                            <input type="radio" name="test" value="big">
                            <img :src="image" alt="Option 2">
                        </label>
                    </div>
                </form>
                <button @click="next"
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
            site: 9,
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
            restriction: {
                type: null,
                age: false,
                grade: false,
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

            }
            // Location
            if (this.site === 8) {

            }
            // Seats
            if (this.site === 7) {

            }
            // Title & Description
            if (this.site === 6) {

            }
            // Restriction
            if (this.site === 5) {

            }
            // Attending
            if (this.site === 4) {

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
                if (this.event.end > this.event.begin) {
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
#esrb [type=radio] + img, #pegi [type=radio] + img,#img [type=radio] + img {
    cursor: pointer;
    height: 50px;
}

/* CHECKED STYLES */
#esrb [type=radio]:checked + img, #pegi [type=radio]:checked + img, #img [type=radio]:checked + img {
    outline: 2px solid #00f;
}
</style>