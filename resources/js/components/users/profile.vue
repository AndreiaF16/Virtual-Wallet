<template>
<div class="content">
    <div class="jumbotron row justify-content-center">
            <h1>{{tittle}}</h1>

    </div>

     <div class="row justify-content-center">
            <img v-bind:src="getActualPhoto()" style="width:130px; height:130px; border-radius:50%; margin-bottom:25px; margin-right:25px; float:left;">
        </div>

    <show-message :class="typeofmsg" :showSuccess="showMessage" :successMessage="message" @close="close"></show-message>

    <error-validation :showErrors="showErrors" :errors="errors" @close="close"></error-validation>


    <div>
        <div class="row justify-content-right">
            <h5><b>{{tittle2}}</b></h5>
        </div>

        <div class="form-group">
            <label for="inputName">Name</label>
            <input type="text" class="form-control" v-model="name"
                    name="name" id="inputName"
                    placeholder="Fullname" value="" />
        </div>

            <div class="form-group">
            <label for="inputNif">Nif</label>
            <input type="number" class="form-control" v-model="nif"
                    name="nif" id="inputNif"
                    placeholder="Nif" value="" />
        </div>

            <div class="form-group">
            <label for="inputEmail">Email</label>
            <input
                type="email" class="form-control" v-model="email"
                name="email" id="inputEmail"
                placeholder="Email address" readonly/>
        </div>

            <div class="form-group">
            <file-upload v-on:fileChanged="onFileChanged"> </file-upload>
        </div>


            <div class="form-group">
                <a class="btn btn-primary" v-on:click.prevent="savedUser">Save Changes</a>
                <a class="btn btn-danger" v-on:click.prevent="cancelEdit">Cancel</a>
        </div>
    <div>
        <br>
        <div class="row justify-content-right">
            <h5>
            <b>{{tittle3}}</b>
            </h5>

        </div>

        <div class="form-group">
            <label for="oldPassword" class="col-sm-4 col-form-label">Current Password</label>
            <div class="col-sm-10">
                <input type="password" name="password_old" class="form-control" id="password_old" v-model="password_old" placeholder="Insert your current password"/>
            </div>
        </div>

        <div class="form-group">
            <label for="newPassword" class="col-sm-4 col-form-label"> New Password</label>
            <div class="col-sm-10">
                <input type="password" name="password" class="form-control" v-model="password" id="password" placeholder="New Password">
            </div>
        </div>

        <div class="form-group">
            <label for="passwordConfirmation" class="col-sm-4 col-form-label"> Password Confirmation</label>
            <div class="col-sm-10">
                <input type="password" name="password_confirmation" class="form-control" v-model="password_confirmation" id="passwordConfirmation" placeholder="Confirm your new password" >
            </div>
        </div>
        </div>

        <div class="form-group">
                <a class="btn btn-primary" v-on:click.prevent="savedPassword">Save Password</a>
                <a class="btn btn-danger" v-on:click.prevent="cancelEdit">Cancel</a>
        </div>
    </div>
</div>
</template>
<script type="text/javascript">
  import errorValidation from '../helpers/showErrors.vue';
    import showMessage from '../helpers/showMessage.vue';
  import fileUpload from '../helpers/uploadFile.vue';

export default {

    data: function() {
      return {
            tittle: 'My Profile',
            tittle2: 'Change Personal Information',
            tittle3:'Change Password',
            errors: [],
            showMessage: false,
            showErrors: false,
            typeofmsg: "",
            message:'',

                email:"",
                name:"",
                nif:'',
                photo:"",
            
            file:'',
            password_old:'',
			password:'',
            password_confirmation:'',
      }
    },

    methods: {
        onFileChanged(fileSelected) {
                this.file = fileSelected
            },
        clear () {
            this.name = ''
        },
        cancelEdit() {
            this.$router.push('/home');
        },
        savedPassword(){

            axios.patch('/api/users/password',
				{
					'password_old':this.password_old,
					'password_confirmation':this.password_confirmation,
                    'password':this.password,
                }).then(response=>{
					this.message='Password updated with success';
                    this.$toasted.success(this.message);
                    this.password_old='';
					this.password_confirmation='';
                    this.password='';

				}).catch(error=>{
                    if(error.response!=undefined){
                        if(error.response.status==401){
                            this.showMessage=true;
                            this.message=error.response.data.unauthorized;
                            this.typeofmsg= "alert-danger";
                            return;
                        }
                        if(error.response.status==422){
                            if(error.response.data.errors==undefined){
                                this.showErrors=false;
                                this.showMessage=true;
                                this.message=error.response.data.password_old;
                                this.typeofmsg= "alert-danger";
                            }else{
                                this.showMessage=false;
                                this.showErrors=true;
                                this.errors=error.response.data.errors;
                            }
                        }
                    }
				});
        },
        savedUser(){
            this.showMessage=false;
            this.showErrors=false;
            let formdata = new FormData();
            formdata.append('name', this.name);
            formdata.append('nif', this.nif || "");
            formdata.append('email', this.email);
            formdata.append('file', this.file);
            formdata.append('_method', 'PUT');
            //https://laracasts.com/discuss/channels/laravel/ajax-formdata-and-put-fails
            axios.post('/api/users/updateProfile', formdata)
                .then(response => {
                    
                    this.message='Profile updated with success';
                    this.$toasted.success(this.message);

                    this.$store.commit('setUser',response.data);
                    localStorage.setItem("user",JSON.stringify(response.data));
                    this.photo = response.data.photo;
                    //this.getActualPhoto();
                })
            .catch(error=>{
                if(error.response != undefined){
                    if(error.response.status==401){
                        this.showMessage=true;
                        this.message=error.response.data.unauthorized;
                        this.typeofmsg= "alert-danger";
                        return;
                    }

                    if(error.response.status==422){
                        if(error.response.data.errors==undefined){
                            this.showErrors=false;
                            this.showMessage=true;
                            this.message=error.response.data.user_already_exists;
                            this.typeofmsg= "alert-danger";
                        }else{
                            this.showMessage=false;
                            this.showErrors=true;
                            this.errors=error.response.data.errors;
                        }
                    }
                }
            });
        },getActualPhoto: function(){
            return 'storage/fotos/'+this.photo || "http://neoleader.com.br/wp-content/uploads/2015/05/geral_adulto-300x300.png";
            },close(){
                this.showErrors=false;
                this.showMessage=false;
            },
    },
    mounted() {
        let user = JSON.parse(localStorage.getItem('user'));
        this.email = user.email;
        this.name = user.name;
        this.photo = user.photo;
        this.nif = user.nif;
    },
     components: {
        'error-validation':errorValidation,
        'show-message':showMessage,
        'file-upload': fileUpload,
    },
}
</script>
