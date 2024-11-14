<template>
    <a href="https://pegi.info/what-do-the-labels-mean" target="_blank"><img :src="pegi" alt="" :class="'h-'+(size?size:16)"></a>
</template>

<script>
export default {
    props: {
        age: {
            type: Number,
            required: true,
        },
        size: {
            type: Number,
        }
    },
    data() {
        return {
            pegi: '/images/pegi/age-3.jpg', // Default PEGI image
        }
    },
    watch: {
        age(val) {
            if (val >= 18) {
                this.pegi = '/images/pegi/age-18.jpg';
            } else if (val >= 16) {
                this.pegi = '/images/pegi/age-16.jpg';
            } else if (val >= 12) {
                this.pegi = '/images/pegi/age-12.jpg';
            } else if (val >= 7) {
                this.pegi = '/images/pegi/age-7.jpg';
            } else {
                this.pegi = '/images/pegi/age-3.jpg';
            }

        }
    }
}
</script>
