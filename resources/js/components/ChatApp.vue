<template>
    <div class="row chat-app" id="app">
        <div class="col-contacts" id="sidebar-menu">
            <contact :contact="user" :isCurrentUser="true" v-on:toggleOptionsMenu="toggleOptionsMenu"></contact>
            <div class="option-menu" ref="optionsMenu">
                <manage-contacts :contacts="contacts" v-on:removeContact="removeContact"></manage-contacts>
                <profile :user="user" v-on:updated="updateCurrentContcat"></profile>
                <password-update></password-update>
                <delete-account></delete-account>
            </div>
            <contact-search></contact-search>
            <div v-if="hasContactRequests" class="contact-requests-received">
                <span v-on:click="showContactRequests">New Contact Requests</span>
                <span v-on:click="showContactRequests" class="contact-requests-received-count"><span>{{contactRequests.length}}</span></span>
            </div>
            <div v-if="hasContacts" class="card card-contacts">
                <div class="card-header">My Contacts</div>
                <div class="card-body">
                    <contact-list :contacts="contacts" @selected="startConversation"/>
                </div>
            </div>   
        </div>
        <div class="col-chat" id="chat-column">
            <div v-if="!isLoading">
            <div class="card card-chat">
                <div class="card-header">
                    <div class="card-header-content">
                        <contact-info :contact="selectedContact" v-if="selectedContact"></contact-info>  
                        <div class="contact-header-title" v-else-if="hasContactRequests">You have new contact requests!</div>      
                        <div class="contact-header-title" v-else-if="hasContacts">Select a Contact...</div>
                        <div class="contact-header-title" v-else>You have no contacts yet... Invite people to chat!</div>
                    </div>
                    <div class="separator"></div>
                </div>
                <div class="card-body">
                <chat v-if="selectedContact" 
                :user="user" 
                :contact="selectedContact" 
                :messages="messages" 
                @new="addNewMessage"
                />
                <div v-else-if="contactRequests" class="contact-request-list">
                    <contact-request-message v-for="contact in contactRequests" 
                    :key="contact.id" 
                    :contact="contact"
                    :isLoading="isLoading"
                    v-on:acceptContactRequest="acceptContactRequest"
                    v-on:rejectContactRequest="rejectContactRequest"
                    >
                    </contact-request-message >
                </div>
                </div>
            </div>  
            </div>
            <div v-else class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
        </div>  
    </div>
</template>

<script>
import Chat from './Chat.vue';
import ContactSearch from './ContactSearch';
import ContactList from './ContactList.vue';
import ContactInfo from './Contact.vue';
import Contact from './Contact';
import ContactRequestMessage from './ContactRequestMessage';
import Profile from './Profile'; 
import PasswordUpdate from './PasswordUpdate';
import DeleteAccount from './DeleteAccount';
import ManageContacts from './ManageContacts';
    export default {
        components: {
            Chat,
            ContactList,
            ContactInfo,
            ContactSearch,
            Contact,
            ContactRequestMessage,
            Profile,
            PasswordUpdate,
            DeleteAccount,
            ManageContacts,
        },
        props:{
            auth: {
                type: Object,
                required: true,
            }
        },
        data(){
            return {
                user: {...this.auth},
                isLoading : true,
                selectedContact: null,
                messages: [],
                contacts: [],
                contactRequests: [],
                imageDefault : ''
            }
        },
        mounted() {
            this.getAvatar();
            this.getContacts();
            this.getContactRequests();
            this.listen();
        },
        methods: {
            toggleOptionsMenu(){
                $(this.$refs.optionsMenu).slideToggle();
            },
            getAvatar(){
                axios.get('api/avatar')
                .then((response) => {
                    if(response.data.avatar && typeof response.data.avatar == 'string'){
                        this.imageDefault = response.data.avatar; 
                    }else{
                        this.imageDefault = "http://via.placeholder.com/150x150";
                    }
                })
                .catch((error) => {
                    this.imageDefault = "http://via.placeholder.com/150x150";
                });
            },
            getContacts(){
                axios.get('api/contacts')
                .then((response) => {
                    this.contacts = response.data;
                    this.isLoading = false;
                })
                .catch((error) => {
                    this.$toastr.error('Contacts cannot be loaded.','Sorry, there was an error');
                });
            },
            getContactRequests(){
                axios.get('api/contacts/initiations/')
                .then((response) => {
                    this.contactRequests = response.data;
                })
                .catch((error) => {
                    this.$toastr.error('Contact requests cannot be loaded.','Sorry, there was an error');
                })
            },
            showContactRequests(){
                this.selectedContact = null;
            },
            acceptContactRequest(id){
                this.isLoading = true;
                axios.post('api/contacts/iniciation/accept',{id : id})
                .then((response) => {
                    this.isLoading = false;
                    //Add to contact list
                    this.contacts.push(response.data.contact_request.from);
                    //Remove accepted contact from the contact requests
                    this.contactRequests = this.removeContactRequest(id);
                    this.$toastr.success('Start Chatting!', 'Contact request Accepted!');
                })
                .catch((error) => {
                    this.isLoading = false;
                    this.handleErrors(error);
                });
            },
            rejectContactRequest(id){
                this.isLoading = true;
                axios.post('api/contacts/iniciation/reject', {id : id})
                .then((response) => {
                    this.isLoading = false;
                    //Remove rejected contact from the contact requests
                    this.contactRequests = this.removeContactRequest(id);
                    this.$toastr.success('Contact request rejected!');
                })
                .catch((error) => {
                    this.isLoading = false;
                    this.handleErrors(error);
                });
            },
            removeContactRequest(id){
                return this.contactRequests.filter((contactReq) => {
                    return contactReq.id != id;
                });
            },
            removeContact(id){
                this.contacts = this.contacts.filter((contact) => {
                    return contact.id != id;
                });
            },
            startConversation(contact){
                this.isLoading = true;
                axios.get(`api/messages/${contact.id}`)
                .then((response) => {
                    this.isLoading = false;
                    this.messages = response.data;
                    this.selectedContact = contact;
                    this.updateUnreadMessagesCount(contact, true);
                })
                .catch((error) => {
                    this.isLoading = false;
                    this.handleErrors(error);
                })
            },
            addNewMessage(message){
                this.messages.push(message);
            },
            updateUnreadMessagesCount(contact, reset){
                //if contact is a contact id, find the contact object
                if(typeof contact != 'object'){
                    contact = this.contacts.find(cont => cont.id == contact)
                }
                this.contacts = this.contacts.map((ct) => {
                    if(ct.id == contact.id){
                        ct.unread_messages_count = reset ? 0 : ct.unread_messages_count+1 ;
                    } 
                    return ct;
                });
            },
            listen(){
                //Listen to incoming messages
                Echo.private(`messages.${this.user.id}`)
                   .listen('NewMessage', (e) => {
                       this.handleRealTimeMessages(e.message);
                });

                //Listen to incoming contact requests
                Echo.private(`contact_initiations_.${this.user.id}`)
                   .listen('NewContactInitiation', (e) => {
                       this.handleRealTimeContactRequests(e.initiation);
                });

                //Listen to incoming contact request status changes
                Echo.private(`contact_initiation_status_change_.${this.user.id}`)
                   .listen('ContactInitiationStatusChange', (e) => {
                        this.handleRealTimeContactRequestStatusChange(e.initiation.contact_request);
                });

                //Listen to incoming massage about being removed from someones contact list
                Echo.private(`removed_from_contacts_.${this.user.id}`)
                   .listen('ContactRemoved', (e) => {
                       this.handleRealTimeContactRemovals(e.contact);
                });

                //Listen to incoming account delete events for users contacts
                Echo.private(`account_deleted_.${this.user.id}`)
                   .listen('AccountDeleted', (e) => {
                       this.handleRealTimeContactRemovals(e.contact);
                });
            },
            handleRealTimeContactRequests(initiation){
                this.contactRequests.push(initiation.contact_request);
            },
            handleRealTimeContactRequestStatusChange(initiation){
                if(initiation.status == 'accepted'){
                    //Add to contact list
                    this.contacts.push(initiation.to);
                    this.$toastr.success('Start Chatting!', `${initiation.to.name} has accepted your contact request.`);
                }else if(initiation.status == 'rejected'){
                    this.$toastr.error('Sorry...', `${initiation.to.name} has rejected your contact request.`);
                }
            },
            handleRealTimeMessages(message){
                if(this.selectedContact && message.from == this.selectedContact.id){
                    // if a message is received from selected contact,set message as read instantly
                     axios.put(`api/messages/${message.id}/read`)
                    .then((response) => {
                        this.addNewMessage(response.data);
                        return;
                    })
                    .catch((error) => {
                        this.updateUnreadMessagesCount(message.from, false);
                    })

                }else{
                    this.updateUnreadMessagesCount(message.from, false);
                }
            },
            handleRealTimeContactRemovals(contact){
                //remove deleted user from contact list if exists
                this.contacts = this.contacts.filter((ct) => {
                    return ct.id != contact.id;
                });
                //remove deleted user from contactRequests, if exists
                  this.contactRequests = this.contactRequests.filter((cnt) => {
                    return cnt.from.id != contact.id;
                  });
                //If deleted user is currently selected as contact, remove selected contact
                 if(this.selectedContact !== null && this.selectedContact.id == contact.id){
                     this.selectedContact = null;
                 } 
                this.$toastr.warning(`User ${contact.name} is no longer available.`);
            },
            handleErrors(error){
                var errMsg = 'Sorry, there was an error';
                if(error.response.status != 500){
                    if(error.response.data.message){
                        errMsg = error.response.data.message;
                    }else if(error.response.data.error){
                        errMsg = error.response.data.error;
                    }
                }
                this.$toastr.error(errMsg);
            
            },
            updateCurrentContcat(contact){
                this.user = {...contact};
            },
        },
        computed:{
            hasContacts: function(){
                return this.contacts.length > 0;
            },
            hasContactRequests: function(){
                return this.contactRequests.length > 0;
            },
        }
    }
</script>
<style lang="scss" scoped>
.chat-app{
    max-width: 100%;
    display:flex;
    .col{
        &-contacts{
            width:330px;
            min-width: 330px;
            transition: all 1s;
        }
        &-chat{
            width: calc(100% - 330px);
            transition: all 1s;
        }
    }
}
.card-body{
    padding:0 !important;
}
.card-header{
    padding: 1rem 2.25rem;
    background:transparent;
}
.card{
    border-radius:0;
    border-left: none;
    border-right: none;
}
.card-contacts{
    .card-header{
        background: transparent;
        text-transform: uppercase;
    }
}
.card-chat{
    border-left: 1px solid rgba(0, 0, 0, 0.125);
    height: 100%;
    border-top:none;
    border-bottom:none;
    min-height: calc(100vh - 40px);

    .card-header{
        border-bottom:none;
        padding: 0 1rem;

        &-content{
            min-height: 90px;
            display: flex;
            align-items: flex-end;

            .contact-header-title{
                padding:1rem 0.5rem
            }
        }
    }
}
.contact-requests-received{
    padding: 1rem 2.25rem;
    padding-right:1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    text-transform: uppercase;
    font-size: 0.85rem;
    border-top: 1px solid rgba(0, 0, 0, 0.125);

    .contact-requests-received-count{
        background: #4db6ac;
        color: #fff;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;

        span{
            font-size: 14px;
            line-height: 14px;
        }
    }
    span:hover{
        cursor:pointer;
    }
}
.contact-request-message{
    margin: 2rem auto;
    max-width: 400px;
    text-align: center;
    box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14),0 3px 1px -2px rgba(0,0,0,0.12),0 1px 5px 0 rgba(0,0,0,0.2);
    padding: 1rem;
}
.loading-spinner{
        width: 100%;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;

        .svg-inline--fa {
            font-size: 2rem;
            color: #4db6ac;
        }
}
.option-menu{
    display:none;
}

</style>