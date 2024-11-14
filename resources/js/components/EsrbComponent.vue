<template>
    <a href="https://www.esrb.org/ratings-guide/" target="_blank"><img :src="pegi" alt="" :class="'h-'+(size?size:16)"></a>
</template>

<script>
export default {
    props: {
        age: {
            type: Number,
            required: true,
        },
        size:{
            type: Number,
        }
    },
    data() {
        return {
            pegi: '/images/esrb/E.svg', // Default PEGI image
        }
    },
    watch: {
        age(val) {
            if (val >= 18) {
                this.pegi = '/images/esrb/AO.svg';
            } else if (val >= 17) {
                this.pegi = '/images/esrb/M.svg';
            } else if (val >= 13) {
                this.pegi = '/images/esrb/T.svg';
            } else if (val >= 10) {
                this.pegi = '/images/esrb/E10plus.svg';
            } else {
                this.pegi = '/images/esrb/E.svg';
            }

        }
    }
}
</script>
