<template>
<div>
    <div class="container">
        <div class="jumbotron row justify-content-center">
                <h1>{{tittle}}</h1>
        </div>
      
        <error-validation :showErrors="showErrors" :errors="errors" @close="close"></error-validation>

        <div class="form-group">
            <label for="inputName">Name</label>
            <input type="name" class="form-control" v-model.trim="user.name" name="name" id="inputName"
            placeholder="Enter name">
        </div>

      <div class="form-group">
        <label for="inputEmail">Email</label>
        <input type="email" class="form-control" v-model="user.email" name="email" id="inputEmail"
        placeholder="Enter email address">
      </div>

      <div class="form-group">
        <label for="inputPassword">Password</label>
        <input type="password" class="form-control" v-model="user.password" name="password" id="inputPassword"
        placeholder="Enter password">
      </div>
       <div class="form-group">
            <file-upload v-on:fileChanged="onFileChanged"> </file-upload>
        </div>
      <div class="form-group">
        <label for="inputNif">Nif</label>
        <input type="text" class="form-control" v-model="user.nif" name="nif" id="inputNif" placeholder="Enter nif">
      </div>

      <div class="form-group">
        <a class="btn btn-primary" v-on:click.prevent="register">Register</a>
        <a class="btn btn-danger" v-on:click.prevent="cancelCreate">Cancel</a>
      </div>
    </div>
  </div>

</template>

<script>
import errorValidation from '../helpers/showErrors.vue';
 import fileUpload from '../helpers/uploadFile.vue';
 
export default {
  data: function() {
    return {
        tittle:'Regist User',
        showErrors:false,
        errors:[],
      user: {
        name: "",
        email: "",
        password: "",
        photo: "",
        nif: "",
      }
    };
  },
  methods: {
    register() {
      let formdata = new FormData();
            formdata.append('name', this.user.name);
            formdata.append('nif', this.user.nif);
            formdata.append('email',this.user.email);
          formdata.append('password',this.user.password);
            formdata.append('photo', this.user.photo);
            formdata.append('_method', 'POST');
      axios
        .post("api/registerUser", formdata)
        .then(response => {
          this.$toasted.success("Register Complete!")
          this.$router.push('/login');
        })
        .catch(error => {
          this.showErrors = true;
          this.errors = error.response.data.errors;
        });
    },onFileChanged(fileSelected) {
        this.user.photo = fileSelected
    },
    close(){
        this.showErrors=false;
    },
    cancelCreate(){
      this.$router.push('/home');
    }
  },
  components: {
      'error-validation':errorValidation,
      'file-upload': fileUpload,
  },
};
</script>

<style>
</style>
