<template>
    <div class="content-holder">
        <div class="top-btn" v-on:click="showDeleteAccountModal">
            <span>
                <span class="icon"><i class="fas fa-user-times mr-1"></i></span>
                <span>Delete Account</span>
            </span>
            <span class="btn-arrow"><i class="fas fa-long-arrow-alt-right"></i></span>
        </div>
        <div>
            <!-- Modal -->
        <div class="modal fade" id="deleteAccountModal" ref="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-times mr-2"></i> Delete Account</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-body-content">
                        <div class="text-center">
                            <h3>Are you sure you want to delete your account?</h3>
                            <p>This action is ireversable. All your contacts and messages will be deleted and you will be logged out.</p>
                        </div>
                    </div>
                </div>
                 <div class="modal-footer">
                        <div v-if="isLoading" class="loading-spinner">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                        <button v-on:click="hideDeleteAccountModal" type="button" class="btn btn-accent-blue" :disabled="isLoading">Cancel</button>
                        <button v-on:click="deleteProfile" type="button" class="btn btn-black" :disabled="isLoading">Delete Account</button>
                </div>
            </div>
        </div>
        </div>
        </div>

    </div>
</template>
<script>
export default {
    data(){
        return {
           authenticated: {},
           errors: [],
           isLoading: false,
        }
    },
    methods:{
        showDeleteAccountModal(){
            $('#deleteAccountModal').modal('show');
        },
        hideDeleteAccountModal(){
            $('#deleteAccountModal').modal('hide');
        },
        deleteProfile(){
            this.isLoading = true;
            axios.post('api/profile/delete')
            .then((response) => {
                this.isLoading = false;
                this.hideDeleteAccountModal();
                this.$toastr.success('Account Deleted');
                if(response.data.redirect_url && response.data.redirect_url == 'string'){
                    window.location.replace(response.data.redirect_url);
                }else{
                    location.reload();
                }
            }).catch((error) => {
                this.isLoading = false;
                this.$toastr.error('Please try again.','Sorry, there was an error');
            });
        },
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
    align-items: center;
    justify-content: space-between;
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

            .modal-body-content{
                display:flex;
                align-items: center;
                justify-content: center;
                min-height: 250px;
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
        .modal-body-content{
            align-items: center;
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