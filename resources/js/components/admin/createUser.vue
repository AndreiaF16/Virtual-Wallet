<template>
    <div >
        <div class="jumbotron row justify-content-center">
            <h1>{{title}}</h1>
        </div>

        <error-validation :showErrors="showErrors" :errors="errors" @close="close"></error-validation>

        <div class="accountCreate-form">
            <div class="form-group">
                <label for="inputName">Name: </label>
                <input type="text" class="form-control" id="inputName" v-model="user.name" placeholder="Name" required>
            </div>

            <div class="form-group">
                <label for="inputEmail">Email: </label>
                <input type="email" class="form-control" id="inputEmail" v-model="user.email" placeholder="Email" required>
            </div>

            <div class="form-group">
            <label for="type">Type:</label>
                <select name="type" id="type" class="form-control" v-model="user.type" required>
                   <option value='' selected> -- Select the Type Of User -- </option>
                    <option value="a">Administrator</option>
                    <option value="o">Operator</option>
                </select>
            </div>

            <div class="form-group">
                <label for="inputPassword">Password: </label>
                <input type="password" class="form-control" id="inputPassword" v-model="user.password" placeholder="Password" required>
            </div>

             <div class="form-group">
            <file-upload v-on:fileChanged="onFileChanged"> </file-upload>
        </div>
            <div class="form-group">
                <a class="btn btn-primary" v-on:click.prevent="register()">Create</a>
                <a class="btn btn-danger" v-on:click.prevent="cancelCreate()">Cancel</a>
            </div>
        </div>
    </div>
</template>

<script>
 import fileUpload from '../helpers/uploadFile.vue';
    import errorValidation from '../helpers/showErrors.vue'
    export default {
        data: function () {
            return {
                title: 'Create Administator or Operator',
               user: {
                    name: '',
                    type: '',
                    email: '',
                    photo: '',
                },
                errors:[],
                showMessage: false,
                showErrors: false,
            }
        },
        methods: {
             register() {
      let formdata = new FormData();
            formdata.append('name', this.user.name);

            formdata.append('email',this.user.email);
            formdata.append('type',this.user.type);
          formdata.append('password',this.user.password);
            formdata.append('photo', this.user.photo);
            formdata.append('_method', 'POST');
      axios
        .post("api/createUser", formdata)
        .then(response => {
            this.successMessage = "User created with sucess!";
            this.$toasted.success(this.successMessage);
            this.$router.push('/home');
        })
        .catch(error => {
          this.showErrors = true;
          this.errors = error.response.data.errors;
        });
    },
    cancelCreate: function(){
        this.$router.push('/home');
    },
    onFileChanged(fileSelected) {
        this.user.photo = fileSelected
    },
    close(){
        this.showErrors=false;
        this.showMessage=false;
    }
    },
    components: {
        'error-validation':errorValidation,
        'file-upload': fileUpload,
    }
}
</script>

<style scoped>
</style>
