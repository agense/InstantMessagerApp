<template>
    <div>
        <div class="contact-search">
            <button class="btn btn-md btn-md-custom btn-accent-beige" v-on:click="showContactList">
                <i class="fas fa-user-plus mr-2"></i>Find Contacts
            </button>
        </div>
        <!-- Modal -->
        <div ref="contactSearchModal" class="modal fade" id="contactSearchModal" tabindex="-1" role="dialog" aria-labelledby="contactSearchModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="contactSearchModalLabel">
                            <i class="fas fa-search mr-2"></i>Search For People You Know...</h5>
                        <button type="btn btn-outline" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="isLoading" class="loading-spinner">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                        <div v-else>
                            <input type="text" v-model="search" placeholder="Search by name or email..." class="form-control">
                            <div v-if="!hasNoMatches" class="contact-list">
                                <contact v-for="contact in matchingContacts" 
                                :key="contact.id" 
                                :contact="contact"
                                :isNotConnected="true"
                                :isLoading="isSending"
                                v-on:sendContactRequest="sendContactRequest">
                                </contact>
                            </div>
                            <div v-else class="no-contacts-found text-center">No Contacts Found</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</template>

<script>
import Contact from './Contact';
export default {
    components:{
        Contact,
    },
    data(){
        return {
            contacts: [],
            search: "",
            isLoading: false,
            isSending: false,
        }
    },
    mounted(){
        $(this.$refs.contactSearchModal).on("hidden.bs.modal", () => {
                this.search = "";
            })
    },
    methods:{
        hideModal(){
            this.search == "";
        },
        showContactList: async function() {
            $('#contactSearchModal').modal('show');
            if(this.contacts.length == 0){
                this.getContactList();
            }
        },
         getContactList(){
            return new Promise((resolve, reject) => {
                this.isLoading = true;
                axios.get('api/contacts/all')
                .then((response) => {
                    this.contacts = response.data;
                    this.isLoading = false;
                    resolve(true);
                })
                .catch((error) => {
                    this.isLoading = false;
                    reject(false);
                });
            });
        },
        sendContactRequest(id){
            this.isSending = true;
            axios.post('api/contacts/iniciate', {
                    to: id,
            })
            .then((response) => {
                this.isSending = false;
                this.contacts = this.contacts.filter((contact) => {
                    return contact.id != id;
                });
                this.$toastr.success('You can start chatting as soon as your contact person accepts your contact request.', 'Contact Request Sent!');
            })
            .catch((error) => {
                this.isSending = false;
                if(error.response.status != 500){
                    this.handleErrors(error.response.data.errors);
                }else{
                    this.$toastr.error('Sorry, there was an error');
                }
            });
        },
        handleErrors(errors){
            let errMsg = '';
            if(typeof errors == 'object'){
                for(let err in errors){
                    if(errors[err][0] && typeof(errors[err][0]) == 'string'){
                        errMsg += `${errors[err][0]} `;
                    }
                }
            }
            errMsg = (errMsg.length > 0) ? errMsg : 'Sorry, there was an error';
            this.$toastr.error(errMsg);
        }
    },
    computed: {
        matchingContacts: function(){
            if(this.search == ""){
                return [];
            }
            return this.contacts.filter((contact) => {
                return ( contact.name.toLowerCase().startsWith(this.search.toLowerCase()) || contact.email.toLowerCase().startsWith(this.search.toLowerCase()));
            });
        },
        hasNoMatches: function(){
            return this.search.length > 0 && this.matchingContacts.length == 0;
        },
    }
}
</script>

<style lang="scss" scoped>
.contact-search{
    padding: 1.5rem 2.25rem;
    text-align: center;
}
.modal-title{
    font-size:1rem;
    color: #989999;
}
.modal-dialog {
    transform: none;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;

    .modal-content{
        min-height: 200px;

        .form-control{
            border: none;
            border-radius: 0;
            background: #f5f5f5;
            margin-bottom:1rem;
        }
        .form-control:focus{
            box-shadow:none;
            border-bottom: 2px solid #4db6ac !important;
        }
    }
    .loading-spinner{
        width: 100%;
        min-height: 135px;
        display: flex;
        align-items: center;
        justify-content: center;

        .svg-inline--fa {
            overflow: visible;
            font-size: 2rem;
            color: #4db6ac;
        }
    }
}
.contact-list{
    max-height: 300px;
    overflow-y: scroll;
}
</style>