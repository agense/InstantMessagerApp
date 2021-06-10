<template>
    <div>
        <div v-if="isSending" class="loading-spinner mt-2">
            <i v-if="isSending" class="fas fa-spinner fa-spin"></i>
        </div>
        <div class="message-form">
            <textarea v-model="message" @keydown.enter.prevent="sendMessage" :disabled="isSending" placeholder="Message..."></textarea>
            <div v-if="errors.message" class="error">{{errors.message[0]}}</div>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return {
            message: "",
            errors: [],
            isSending: false,
        }
    },
    props:{
        contact: {
            type: Object,
            required: true
        },
    },
    methods:{
        sendMessage(){
            if(this.message == ""){
                return;
            }
            if(!this.contact) return;
            this.errors = [];
            this.isSending = true;
            axios.post('api/messages/send', {
                'contact_id': this.contact.id,
                'message' : this.message
            }).then((response) => {
                this.isSending = false;
                this.$emit('new', response.data);
                this.message = "";
            }).catch((error) => {
                this.isSending = false;
                if(error.response.status == 422){
                    this.errors = error.response.data.errors;
                    if(error.response.data.errors.contact_id){
                        this.$toastr.error(error.response.data.errors.contact_id[0]);
                    }
                }else{
                    this.$toastr.error('Please try again.','Sorry, there was an error');
                }
            });
        },
    }
}
</script>
<style lang="scss" scoped>
.message-form {
    textarea{
        width:94%;
        margin:10px 2%;
        padding:10px;
        resize:none;
        border-radius:3px;
        border:1px solid lightgray
    }
}
.error{
    padding: 0 1.5rem;
    font-size: 12px;
    color: #BD362F;
}
.loading-spinner{
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;

        .svg-inline--fa {
            overflow: visible;
            font-size: 1rem;
            color: #4db6ac;
        }
    }

</style>