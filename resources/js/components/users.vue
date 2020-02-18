<template>
    <div>
        <div class="jumbotron">
            <h1>{{ title }}</h1>
        </div>

        <user-list 
            :users="users" 
            :current-user="currentUser"
            @delete-user="deleteUser" 
            @edit-user="editUser" 
            ref="userListReference">
        </user-list>

        <user-edit 
            v-if="editingUser"
            :departments="departments"
            :user="currentUser"
            @save-user="saveUser"
            @cancel-edit="cancelEdit">
        </user-edit>     

        <div class="alert alert-success" v-if="showSuccess">
            <button type="button" class="close-btn" v-on:click="showSuccess=false">&times;</button>
            <strong>{{ successMessage }}</strong>
        </div>

        
    </div>
</template>

<script>
    import UserListComponent from './userList';
    import UserEditComponent from './userEdit';

    export default {
        data: function () {
            return {
                title: 'List Users',
                editingUser: false,
                showSuccess: false,
                showFailure: false,
                successMessage: '',
                failMessage: '',
                currentUser: null,
                users: [],
                departments: []
            };
        },
        methods: {
            editUser: function (user) {
                this.currentUser = user;
                this.editingUser = true;
                this.showSuccess = false;

                
            },
            deleteUser: function (user) {
                axios.delete('api/users/' + user.id)
                    .then(response => {
                        this.showSuccess = true;
                        this.successMessage = 'User Deleted';
                        this.getUsers();
                    });
            },
            saveUser: function (user) {
                this.editingUser = false;
                axios.put('api/users/' + user.id, user)
                    .then(response => {
                        this.showSuccess = true;
                        this.successMessage = 'User Saved';
                        // Copies response.data.data properties to this.currentUser
                        // without changing this.currentUser reference
                        Object.assign(this.currentUser, response.data.data);
                        this.currentUser = null;
                        this.editingUser = false;
                        //this.$refs.UserListReference.currentUser = null;
                    });
            },
            cancelEdit: function () {
                this.showSuccess = false;
                this.editingUser = false;
                axios.get('api/users/' + this.currentUser.id)
                    .then(response => {
                        console.dir(this.currentUser);
                        // Copies response.data.data properties to this.currentUser
                        // without changing this.currentUser reference
                        Object.assign(this.currentUser, response.data.data);
                        console.dir(this.currentUser);
                        //this.$refs.UserListReference.currentUser = null;
                    });
            },
            getUsers: function () {
                axios.get('api/users')
                    .then(response => { this.users = response.data.data; });
            },
        },
        mounted() {
            this.getUsers();
            // axios.get('api/departments')
            //     .then(response => { this.departments = response.data.data; });
        },
        components:{
            'user-list':UserListComponent,
            'user-edit':UserEditComponent
        }

    }
</script>

<style>

</style>

