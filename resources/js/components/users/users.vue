<template>
<div>
    <div class="jumbotron">
    <h1>{{ title }}</h1>
</div>

<users-list :users="users" @edit-user="editUser" @delete-user="deleteUser" ref="userListReference"> </users-list>
<div class="alert alert-success" v-if="showSuccess">
    <button type="button" class="close-btn" v-on:click="showSuccess=false">&times;</button>
    <strong>{{ successMessage }}</strong>
</div>
<users-edit  :currentUser='currentUser' @save-user="saveUser" @cancel-user="cancelEdit" v-if="editingUser"> </users-edit>

</div>
</template>

<script>

import UsersList from './profile'

export default {
    data: function(){
        return{
            title: 'List Users',
            editingUser: false,
            showSuccess: false,
            showFailure: false,
            successMessage: '',
            failMessage: '',
            currentUser: null,
            users: [],
            departments: []
        }
    },
    methods: {
        editUser: function(user){
            this.currentUser = user;
            this.editingUser = true;
            this.showSuccess = false;

        },

        deleteUser: function(user){
            axios.delete('api/users/'+user.id)
                .then(response => {
                    this.showSuccess = true;
                    this.successMessage = 'User Deleted';
                    this.getUsers();
                });
        },
        saveUser: function(){
            this.editingUser = false;
            this.$refs.userListReference.currentUser = null;
            axios.put('api/users/'+this.currentUser.id,this.currentUser)
                .then(response=>{
                    this.showSuccess = true;
                    this.successMessage = 'User Saved';
                    // Copies response.data.data properties to this.currentUser
                    // without changing this.currentUser reference
                    Object.assign(this.currentUser, response.data.data);
                    this.currentUser = null;
                    this.editingUser = false;
                });
        },
        cancelEdit: function(){
            this.$refs.userListReference.currentUser = null;
            this.showSuccess = false;
            this.editingUser = false;
            axios.get('api/users/'+this.currentUser.id)
                .then(response=>{
                    console.dir (this.currentUser);
                    // Copies response.data.data properties to this.currentUser
                    // without changing this.currentUser reference
                    Object.assign(this.currentUser, response.data.data);
                    console.dir (this.currentUser);
                    this.currentUser = null;
                });
        },
        getUsers: function(){
            axios.get('api/users')
                .then(response=>{this.users = response.data.data;});
        }
    },
    components:{
        "users-list" :UsersList,
      //  "users-edit" :UsersEdit
    },
   mounted() {
        this.getUsers();
        axios.get('api/departments')
            .then(response=>{this.departments = response.data.data; });
    }
}
</script>
<style scoped>

</style>


