<template>
    <div class="contact" v-bind:class="{ contact_own_user: isCurrentUser, contact_in_search: isNotConnected, contact_in_managed: inManagedContacts }" >
        <div class="avatar">
            <img v-if="contact.profile_image" :src="contact.profile_image" alt="">
            <img v-else :src="this.$parent.imageDefault ? this.$parent.imageDefault : this.$parent.$parent.imageDefault" alt="">
        </div>
        <div class="contact-info">
            <p class="contact-name">{{contact.name}}</p>
            <p class="contact-email">{{contact.email}}</p>
        </div>
        <div v-if="isCurrentUser" v-on:click="toggleOptionsMenu" class="options-menu-toggler">
            <i class="fas fa-ellipsis-h"></i>
        </div>
        <div v-if="isNotConnected" class="contact-request">
            <button class="btn btn-sm btn-sm-custom btn-accent-blue" 
            :disabled="isLoading"
            v-on:click="sendContactRequest(contact.id)">
            <span v-if="(isLoading && activeContactId == contact.id)"><i class="fas fa-spinner fa-spin mr-1"></i></span> Add Contact
            </button>
        </div>
        <div v-if="inManagedContacts" class="contact-request">
            <button class="btn btn-sm btn-sm-custom btn-black" 
            :disabled="isLoading"
            v-on:click="removeContact(contact.id)">
            <span v-if="(isLoading && activeContactId == contact.id)"><i class="fas fa-spinner fa-spin mr-1"></i></span> Remove
            </button>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return {
            activeContactId : null,
        }
    },
    props:{
        contact: {
            type: Object,
            required: true,
        },
        isCurrentUser:{
            type: Boolean,
            default:false,
        },
        isNotConnected:{
            type: Boolean,
            default:false,
        },
        inManagedContacts: {
            type: Boolean,
            default:false,
        },
        isLoading: {
            type: Boolean,
            default:false,
        }
    },
    methods: {
        toggleOptionsMenu(){
            this.$emit('toggleOptionsMenu');
        },
        sendContactRequest(id){
           this.activeContactId = id;
           this.$emit('sendContactRequest', id);
        },
        removeContact(id){
           this.activeContactId = id;
           this.$emit('removeContact', id);
        }
    },
}
</script>
<style lang="scss" scoped>
.contact {
        display: flex;
        padding: 1rem;
        height: 80px;
        position: relative;
        flex: 5;
        
    &_own_user{
        min-height: 90px;
        background: #4db6ac;
        color: #fff;
        padding-right: 35px;

        .contact-name{
            text-transform: uppercase;
            letter-spacing: 0.05rem;
        }

        .options-menu-toggler{
            height: 20px;
            position: absolute;
            right: 1.25rem;
            top: calc(50% - 10px);

            .svg-inline--fa{
                font-size: 20px;
            }
        }
        .options-menu-toggler:hover{
            cursor:pointer;
        }
    }
    &_in_search, &_in_managed{
        border-bottom: 1px dotted #989898;
        padding: 1.5rem;
        margin: 0 1em;
        padding: 1rem 0;

        .contact-info{
            flex:3;
        }
    }
    .avatar{
        display:flex;
        align-items: center;
        max-width: 70px;
        margin-right: 15px;

        img{
            width:40px;
            height: 40px;
            border-radius:50%;
            margin: 0 auto;
        }
    }
    .contact-info{
        display:flex;
        flex-direction: column;
        justify-content: center;
        overflow: hidden;
        font-size:12px;

        p{
            margin:0;
            &.contact-name{
                font-weight: bold;
            }
        }
    }
}
.contact-request-message .contact{
        justify-content: center;

         .contact-info {
            text-align: left;
        }
    }
</style>