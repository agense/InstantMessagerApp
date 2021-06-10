<template>
    <div class="contact-request-message" v-if="!isLoading">
        <contact :contact="contact.from"></contact>
        <div class="contct-request-message-handlers">
            <button class="btn btn-sm btn-sm-custom btn-accent-blue mr-1" v-on:click="acceptContactRequest(contact.id)">Accept</button>
            <button class="btn btn-sm btn-sm-custom btn-black ml-1" v-on:click="rejectContactRequest(contact.id)">Reject</button>
        </div>
    </div>
</template>

<script>
import Contact from './Contact';
export default {
    components:{
        Contact,
    },
    props:{
        contact: {
            type: Object,
            required: true,
        },
        isLoading: {
            type: Boolean,
            required: true,
        },
    },
    methods: {
        acceptContactRequest(id){
            this.$emit('acceptContactRequest', id);
        },
        rejectContactRequest(id){
            this.$emit('rejectContactRequest', id);
        }
    }
}
</script>

