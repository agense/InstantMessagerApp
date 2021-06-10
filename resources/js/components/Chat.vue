<template>
    <div class="chat-area">
        <message-feed :contact="contact" :user="user" :messages="messages"/>
        <message-form :contact="contact" @new="newMessage" /> 
    </div>
</template>
<script>
import MessageFeed from './MessageFeed.vue';
import MessageForm from './MessageForm.vue';
export default {
    components:{
        MessageFeed,
        MessageForm
    },
    props:{
        user: {
            type: Object,
            required: true,
        }, 
        contact: {
            type: Object,
            default: null
        },
        messages: {
            type: Array,
            default: []
        }
    },
    methods:{
        newMessage(text){
            this.$emit('new', text);
        }
    }
}
</script>
<style lang="scss" scoped>
.chat-area{
    flex:5;
    display: flex;
    flex-direction: column;
    justify-content: space-between;

    h1{
        font-size:20px;
        padding:10px;
        margin:0;
        border-bottom: 1px dashed lightgray;
    }
}
</style>