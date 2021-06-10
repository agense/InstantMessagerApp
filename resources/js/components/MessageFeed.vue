<template>
    <div class="feed" ref="feed">
        <ul v-if="contact">
            <li v-for="message in messages" :key="message.id" 
            :class="`message ${message.to == contact.id ? 'sent' : 'received'}`">
            <div class="message-display">
                <small class="message-sender">{{message.from == contact.id ? contact.name : user.name}}</small>
                <small class="message-date"><span> | {{message.created_at}}</span></small>
                <div class="message-text">
                    {{message.message}}
                </div>
            </div>
            </li> 
        </ul>
    </div>
</template>
<script>
export default {
    props:{
         user: {
            type: Object,
            required: true,
        }, 
        contact: {
            type: Object,
            required: true,
        },  
        messages:{
            type: Array,
            required: true
        }  
    },
    mounted() {
        if(this.contact && this.messages.length > 0){
            this.scrollToBottom();
        }
    },
    methods:{
        scrollToBottom(){
            setTimeout(() => {
                this.$refs.feed.scrollTop = this.$refs.feed.scrollHeight - this.$refs.feed.clientHeight;
            }, 50); 
        }
    },
    watch: {
        contact(contact){
            if(this.contact && this.messages.length > 0){
                this.scrollToBottom();
            }
        },
        messages(messages){
            this.scrollToBottom();
        }
    }
}
</script>
<style lang="scss" scoped>
    .feed{
        height:calc(100vh - 280px);
        min-height: calc(100vh - 280px);
        overflow-y:scroll;
        padding:1rem;

        ul{
            list-style-type: none;
            padding:5px;

            li{
                &.message{
                    margin: 10px 0;
                    width:100%;

                    .message-display{
                        width:45%;
                        display:inline-block;
                    }
                    .message-text{
                        width:100%;
                        border-radius:5px;
                        padding:12px;
                        display:block;
                    }
                    .message-date{
                        color: #989898;
                        margin-bottom: 0.4rem;
                        display: inline;
                    }
                    .message-sender{
                        color: #989898;
                        display: inline;
                    }

                    &.received{
                        text-align: right;

                        .message-text{
                            background: #e0f2f1;
                        }
                    }
                    &.sent{
                        text-align: left;

                        .message-text{
                            background:#f3f6f7;
                        }
                    }
                }
            }
        }
    }
</style>