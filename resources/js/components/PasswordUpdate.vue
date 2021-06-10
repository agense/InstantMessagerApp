<template>
    <div class="contect-holder">
        <div class="top-btn" v-on:click="showPasswordUpdateModal">
            <span>
                <span class="icon"><i class="fas fa-user-lock mr-1"></i></span>
                <span>Security</span>
            </span>
            <span class="btn-arrow"><i class="fas fa-long-arrow-alt-right"></i></span>
        </div>
        <div>
            <!-- Modal -->
        <div class="modal fade" id="passwordModal" ref="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-lock mr-2"></i>Update Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-body-content">
                        <div class="modal-body-data">
                            <div class="field-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-unlock-alt"></i></div>
                                    </div>
                                    <input type="password" v-model="form.current_password" placeholder="Current Password" class="form-control">
                                </div>
                                <div v-if="errors.current_password" class="error">{{errors.current_password[0]}}</div>
                            </div>
                            <div class="field-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-lock"></i></div>
                                    </div>
                                    <input type="password" v-model="form.password" placeholder="New Password" class="form-control">
                                </div>
                                <div v-if="errors.password" class="error">{{errors.password[0]}}</div>
                            </div>
                            <div class="field-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-lock"></i></div>
                                    </div>
                                    <input type="password" v-model="form.password_confirmation" placeholder="Confirm Password" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="modal-footer">
                        <div v-if="isLoading" class="loading-spinner">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                        <button v-on:click="updatePassword" type="button" class="btn btn-accent-blue" :disabled="isLoading">Update Password</button>
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
           form: {
            password: "",
            password_confirmation: "",
            current_password: "",
           },
           errors: [],
           isLoading: false,
        }
    },
    mounted(){
        $(this.$refs.passwordModal).on("hidden.bs.modal", () => {
            this.form = {};
        })
    },
    methods:{
        showPasswordUpdateModal(){
            $('#passwordModal').modal('show');
        },
        hidePasswordUpdateModal(){
            $('#passwordModal').modal('hide');
        },
        updatePassword(){
            this.errors = [];
            this.isLoading = true;
            axios.put('api/profile/security/update', {
                ...this.form
            }).then((response) => {
                this.isLoading = false;
                this.form = {};
                this.hidePasswordUpdateModal();
                this.$toastr.success('Password Updated!');
            }).catch((error) => {
                this.isLoading = false;
                if(error.response.status == 422){
                    this.errors = error.response.data.errors;
                }else{
                    this.$toastr.error('Please try again.','Sorry, there was an error');
                }
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
            padding: 2rem;
            padding-top:1rem;
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

        .field-group{
            margin-bottom:1rem;

            .input-group-text{
                width: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                color:#4db6ac;
            }
            .error{
                padding: 5px;
                font-size: 12px;
                color: #BD362F;
            }
            .form-control{
                background: #f5f5f5;
            }
            .form-control:focus{
                box-shadow:none;
            }
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