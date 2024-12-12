<template>

    <div class="mx-auto space-y-6 text-black ">
        <div>
            <h1 class="text-center">
                Verifisering med SMS
            </h1>
            <p class="text-sm font-light leading-none text-center">

            </p>
        </div>
        <form @submit.prevent class="flex mx-auto justify-center items-center gap-2" autocomplete="off" id="validate">
            <input v-for="(field, index) in fields"
                   :key="index"
                   type="text"
                   class="inline-flex items-center justify-center bg-gray-200 border rounded-md border-gray-500 w-12 h-12 text-sm font-light text-center focus:outline-none focus:border-[#fc6736] text-black"
                   v-model="fields[index]"
                   @keydown="handleKeydown($event, index)"
                   @input="handleInput($event, index)"
            />
        </form>

        <div class="text-center">
            <p class="text-sm font-light leading-none text-center text-gray-400">
                Valideringsfrist:
                <span class="text-[#fc6736]">
              <span>{{ time.hours }}</span>
              <span>:</span>
              <span>{{ time.minutes }}</span>
              <span>:</span>
              <span>{{ time.seconds }}</span>
            </span>
            </p>
        </div>
    </div>
</template>

<script setup>
import {onMounted, ref} from 'vue';

const fields = ref(['', '', '', '']);
const time = ref({hours: '00', minutes: '00', seconds: '00'});

const makeCountDown = (endTime) => {
    const update = () => {
        const now = new Date();
        const timeLeft = endTime - now.getTime();

        if (timeLeft <= 0) {
            time.value = {hours: '00', minutes: '00', seconds: '00'};
            return;
        }

        const hours = Math.floor(timeLeft / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

        time.value.hours = hours > 9 ? hours : '0' + hours;
        time.value.minutes = minutes > 9 ? minutes : '0' + minutes;
        time.value.seconds = seconds > 9 ? seconds : '0' + seconds;
    };

    setInterval(update, 1000);
    update();
};

const handleKeydown = (event, index) => {
    const {keyCode} = event;

    if (keyCode === 8) return true;
    if (keyCode >= 48 && keyCode <= 67) return true;

    event.preventDefault();
    return false;
};

const handleInput = (event, index) => {
    const {value} = event.target;

    if (value.length === 1 && index !== fields.value.length - 1) {
        document.querySelectorAll('input[type="text"]')[index + 1].focus();
    } else if (value.length === 0 && index !== 0) {
        document.querySelectorAll('input[type="text"]')[index - 1].focus();
    }
};

onMounted(() => {
    const endTime = new Date().getTime() + 300000; // 5 minutter fra nå
    makeCountDown(endTime);
});
</script>
