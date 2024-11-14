<template>

</template>
<script>
export default {
    props:{
        hasErrors:{
            type: Object,
            required: true
        },
    },
    emits:['sendErrors', 'close', 'familyName'],
    data(){
        return{

        }
    }
}
</script>
