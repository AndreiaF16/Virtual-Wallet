<template>
<div>
    <div class="jumbotron">
            <h1>User Login</h1>
        </div>
        <show-message :class="typeofmsg" :showSuccess="showMessage" :successMessage="message" @close="close"></show-message>
        <form>
            <div class="form-group">
                <label for="inputEmail">Email</label>
                <input type="email" class="form-control" v-model.trim="user.email"
                       name="email" id="inputEmail"
                       placeholder="Email address" value="" />
            </div>
            <div class="form-group">
                <label for="inputPassword">Password</label>
                <input type="password" class="form-control" v-model="user.password"
                       name="password" id="inputPassword"
                       placeholder="Password" value="" />
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary" v-on:click.prevent="userLogin()">Login</button>
                <button type="submit" class="btn btn-danger" v-on:click.prevent="cancelLogin()">Cancel</button>
            </div>
        </form>
    </div>
</template>
<script type="text/javascript">

    import showMessage from './helpers/showMessage.vue';

    export default {
        data: function () {
            return {
                message: "",
                typeofmsg: "alert-success",
                showMessage: false,
                user: {
                    email:"",
                    password:"",
                    remember_token: null,
                },
            }
        },
        methods: {
            userLogin(){
                axios.post('/api/login', this.user)
                    .then(response => {
                        axios.defaults.headers.common.Authorization = "Bearer " + response.data.access_token;
                        this.$store.commit('setToken',response.data.access_token);
                        this.$store.commit('logIn',true);
                        localStorage.setItem("token", response.data.access_token);
                        axios.get('/api/users/me')
                            .then(response => {
                                this.$store.commit('setUser',response.data.data);
                                localStorage.setItem("user",JSON.stringify(response.data.data));
                                this.$socket.emit("user_enter",response.data.data);
                                this.$toasted.success("Welcome "+ response.data.data.name +"!")
                            });
                        this.$router.push('/home');
                    })
                    .catch(error => {
                        this.showMessage=true;
                        this.message = "Invalid credentials";
                        this.typeofmsg= "alert-danger";
                        /*if(error.response.status==401){
					        this.$toasted.error(error.response.data.unauthorized);
                        }else if(error.response.status == 422){
                            this.$toasted.error(error.response.data.message)
                        }else{
                            this.$toasted.error(error.response.data.msg);
                        }*/
                        return;
                    });
            },
             close(){
                this.showMessage = false;
            },
            cancelLogin() {
                this.$router.push('/home');
            }
        },
        components: {
        'show-message':showMessage,
    },

    };
</script>
