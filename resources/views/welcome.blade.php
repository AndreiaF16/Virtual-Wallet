<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Virtual Wallet</title>
    </head>
    <body>

       <!--     <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                        <div class="navbar-nav">-->

       <div id="app">
            <div class="w3-sidebar w3-light-grey w3-bar-block" style="width:13%">
                    <h3 class="w3-bar-item">Menu</h3>
           <!--     <a href="" id="mySidebar" role="button"
                aria-haspopup="true" aria-expanded="false">Virtual Wallet</a>-->

                <router-link class="nav-item nav-link" to="/login"  v-show="!this.$store.state.user">Login</router-link>
                <router-link class="nav-item nav-link" v-on:click.native="logout" v-show="this.$store.state.user" to="/">Logout</router-link>
                <router-link class="nav-item nav-link" to="/home">Home</router-link>
                <router-link class="nav-item nav-link" to="/profile" v-show="this.$store.state.user">Profile</router-link>
                <router-link class="nav-item nav-link" to="/operator" v-show="this.$store.state.user && this.$store.state.user.type=='o'">Create an income</router-link>
                <router-link class="nav-item nav-link" to="/register" v-show="!this.$store.state.user"> Create an account </router-link>
                <router-link class="nav-item nav-link" to="/myVirtualWallet" v-show="this.$store.state.user && this.$store.state.user.type=='u'">My Virtual Wallet</router-link>
                <router-link class="nav-item nav-link" to="/createUser" v-show="this.$store.state.user && this.$store.state.user.type=='a'">Create User</router-link>
                <router-link class="nav-item nav-link" to="/users" v-show="this.$store.state.user && this.$store.state.user.type=='a'">Users</router-link>
                <router-link class="nav-item nav-link" to="/debit" v-show="this.$store.state.user && this.$store.state.user.type=='u'">Register Debit</router-link>
                <router-link class="nav-item nav-link" to="/movementStatistics" v-show="this.$store.state.user && this.$store.state.user.type=='u'">Movement Statistics</router-link>
                <router-link class="nav-item nav-link" to="/statistics" v-show="this.$store.state.user && this.$store.state.user.type=='a'">Statistics</router-link>


        </div>

        <div style="margin-left:17%">
                <div style="margin-right:2%">

                <br>
    <em>Name of The User: @{{this.$store.state.user != null ? this.$store.state.user.name : " ATENTION: ** No User Logged in ** " }}</em>
            <router-view></router-view>
        </div>
       </div>
    </div>
      <script src="js/app.js">  </script>

    </body>
</html>
