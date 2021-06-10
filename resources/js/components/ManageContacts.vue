<template>
    <div class="content-holder">
        <div class="top-btn" v-on:click="showContactManagementModal">
            <span>
                <span class="icon"><i class="fas fa-user-friends mr-1"></i></span>
                <span>My Contacts</span>
            </span>
            <span class="btn-arrow"><i class="fas fa-long-arrow-alt-right"></i></span>
        </div>
        <div>
            <!-- Modal -->
        <div class="modal fade" id="contactManagementModal" ref="contactManagementModal" tabindex="-1" role="dialog" aria-labelledby="contactManagementModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-friends mr-2"></i> Manage Contacts</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="contact-list">
                        <ul v-if="contacts.length > 0">
                            <li v-for="contact in contacts" :key="contact.id" >
                                <contact :contact="contact" 
                                :inManagedContacts="true" 
                                :isLoading="isLoading" 
                                v-on:removeContact="removeContact">
                                </contact>   
                            </li>
                        </ul>
                        <div v-else class="no-contacts-found text-center">No Contacts Found</div>
                    </div>
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
    props:{
        contacts:{
            type: Array,
            required: true,
        }
    },
    data(){
        return {
           isLoading: false,
        }
    },
    methods:{
        showContactManagementModal(){
            $('#contactManagementModal').modal('show');
        },
        removeContact(id){
            this.isLoading = true;
            axios.post('api/contacts/remove', {
                id: id,
            })
            .then((response) => {
                this.isLoading = false;
                this.$emit('removeContact', response.data.id);
                this.$toastr.success('Contact removed.');
            })
            .catch((error) => {
                this.isLoading = false;
                this.$toastr.error('Please try again.','Sorry, there was an error');
            })
        }
    }
}
</script>
<style lang="scss" scoped>
.top-btn{
    padding: 1rem 1.25rem;
    text-transform: uppercase;
    font-size: 14px;
    letter-spacing: 0.1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f7f7f7;

    span{
        padding: 0;
        margin: 0;
        line-height: 18px;
    }
    span.icon{
        display:inline-block;
        margin-right: 0.5rem;
        width: 25px;

        .svg-inline--fa{
            color: #4db6ac;
            font-size: 18px;
        }
    }   
    .btn-arrow{
        display:none;
        
        .svg-inline--fa{
            color: #4db6ac;
            font-size: 18px;
        }
    }
    
}
.top-btn:hover{
    cursor: pointer;
    background: #efefef;

    .btn-arrow{
        display:block;
    }
}
.modal-title{
    font-size:1rem;
    color: #989999;
    text-transform: uppercase;
}
.modal-dialog {
    transform: none;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;

    .modal-content{
        min-height: 400px;

        .modal-body{
            padding: 1rem 2rem;

                .contact-list{
                    flex:2;
                    max-height:600px;
                    min-height:350px;
                    overflow-y: auto;
                    ul{
                        list-style-type: none;
                        padding-left : 0;

                        li{
                            border-bottom: 1px dotted #989898;
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            cursor: pointer;

                            li:hover{
                                background: #efefef;
                            }
                        }
                    }
                }
        }
        .modal-footer{
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-header {
            padding: 1rem 2rem;
        }
    }
    .loading-spinner{
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 2rem;

        .svg-inline--fa {
            overflow: visible;
            font-size: 1rem;
            color: #4db6ac;
        }
    }
}

</style>