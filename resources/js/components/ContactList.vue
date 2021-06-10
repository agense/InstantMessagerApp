<template>
    <div class="contact-list">
        <ul v-if="contacts">
            <li v-for="contact in sortedContacts" :key="contact.id" @click="selectContact(contact)"
            :class="{ 'selected' : contact == selected }">
                <contact :contact="contact"></contact>   
                <span class="unread-message-count" v-if="contact.unread_messages_count">
                    <span>{{contact.unread_messages_count}}</span>
                </span>
            </li>
        </ul>
    </div>
</template>
<script>
import Contact from './Contact.vue';
export default {
    components:{
        Contact,
    },
    props: {
        contacts: {
            type: Array,
            default : [],
        }
    },
    data(){
        return {
            selected: null,
        }
    },
    methods: {
        selectContact(contact){
            this.selected = contact;
            this.$emit('selected', contact)
        }
    },
    computed:{
        sortedContacts(){
            return _.sortBy(this.contacts, [contact => {
                if(contact == this.selected){
                    return Infinity;
                }
                return contact.unread_messages_count;
            }]).reverse();
        }
    }
}
</script>
<style lang="scss" scoped>
.contact-list{
    flex:2;
    max-height:600px;
    overflow-y: scroll;
    border-left: 1px solid #eee;
    min-height:350px;
}
ul{
    list-style-type: none;
    padding-left : 0;

    li{
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;

        &.selected{
            background: #efefef;
        }
    }
    .unread-message-count{
        background: #4db6ac;
        color: #fff;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.9rem;

        span{
            font-size: 14px;
            line-height: 14px;
        }
    }
}

</style>