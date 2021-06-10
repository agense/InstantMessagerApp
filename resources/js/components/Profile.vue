<template>
    <div class="content-holder">
        <div class="top-btn" v-on:click="showProfileModal">
            <span>
                <span class="icon"><i class="fas fa-user-circle mr-1"></i></span>
                <span>My Profile</span>
            </span>
            <span class="btn-arrow"><i class="fas fa-long-arrow-alt-right"></i></span>
        </div>
        <div>
            <!-- Modal -->
        <div class="modal fade" id="profileModal" ref="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-circle mr-2"></i>My Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-body-content">
                        <div class="profile-image">
                            <div class="profile-image-holder" >
                                <img v-if="authenticated.profile_image" :src="authenticated.profile_image" alt="">
                                <img v-else :src="this.$parent.imageDefault" alt="">
                            </div>
                             <div class="field-group upload-buttons py-1">
                                 <div v-if="errors.profile_image" class="error text-center mb-2">{{errors.profile_image[0]}}</div>
                                 <div class="field-group">
                                    <label for="profile-image" class="btn btn-sm-custom btn-accent-blue-border mr-1">Upload Profile Image</label>
                                    <input type="file" id="profile-image" value="" ref="profileImage" v-on:change="replaceImage" hidden >
                                    <button v-on:click="removeImage" type="button" class="btn btn-sm-custom btn-black-border ml-1">Remove Profile Image</button>
                                </div>
                            </div>
                        </div>
                        <div class="profile-data">
                            <div class="field-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="far fa-user"></i></div>
                                    </div>
                                    <input type="text" v-model="authenticated.name" placeholder="Name" class="form-control">
                                </div>
                                <div v-if="errors.name" class="error">{{errors.name[0]}}</div>
                            </div>
                            <div class="field-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-envelope-open"></i></div>
                                    </div>
                                    <input type="text" v-model="authenticated.email" placeholder="Email" class="form-control">
                                </div>
                                <div v-if="errors.email" class="error">{{errors.email[0]}}</div>
                            </div>
                            <div class="field-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-mobile-alt"></i></div>
                                    </div>
                                    <input type="text" v-model="authenticated.phone" placeholder="Phone" class="form-control">
                                </div>
                                <div v-if="errors.phone" class="error">{{errors.phone[0]}}</div>
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="modal-footer">
                        <div v-if="isLoading" class="loading-spinner">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                        <button v-on:click="updateProfile" type="button" class="btn btn-accent-blue" :disabled="isLoading">Update Account</button>
                </div>
            </div>
        </div>
        </div>
        </div>

    </div>
</template>
<script>
export default {
    props:{
        user: {
            'type' : Object,
            'required': true,
        }
    },
    data(){
        return {
           authenticated: {},
           errors: [],
           isLoading: false,
        }
    },
    mounted(){
        this.authenticated = {...this.user};
        $(this.$refs.profileModal).on("hidden.bs.modal", () => {
            this.authenticated = {...this.user};
        })
    },
    methods:{
        showProfileModal(){
            $('#profileModal').modal('show');
        },
        updateProfile(){
            this.errors = [];
            this.isLoading = true;
            axios.put('api/profile/update', {
                ...this.authenticated,
            }).then((response) => {
                this.isLoading = false;
                this.authenticated = {...response.data};
                this.$emit('updated', response.data);
                this.$toastr.success('Profile Updated!');
            }).catch((error) => {
                this.isLoading = false;
                if(error.response.status == 422){
                    this.errors = error.response.data.errors;
                }else{
                    this.$toastr.error('Please try again.','Sorry, there was an error');
                }
            });
        },
        replaceImage(){
            //Create image display
            let reader = new FileReader();
            reader.addEventListener('load', function(){
               let profileImg = reader.result;
               this.authenticated.profile_image = profileImg;
            }.bind(this), false);
            reader.readAsDataURL(this.$refs.profileImage.files[0]);
        },
        removeImage(){
            this.authenticated.profile_image = null;
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

            .profile-image-holder{
                margin: 1rem auto;
                border: 1px solid #ced4da;
                min-height: 150px;
                height: 150px;
                width: 150px;
                max-width: 150px;
                border-radius: 50%;

                img{
                    width:100%;
                    height:100%;
                    object-fit: cover;
                    border-radius: 50%;
                }
            }
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
    .upload-buttons .field-group{
        display: flex;
        justify-content: center;
        align-items: center;

        label{
            margin-bottom:0;
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