<template>
    <div class="title m-b-md">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card card-default">
                        <div class="jumbotron row justify-content-center">
                            <h1>{{title}}</h1>
                        </div>

                        <div class="row justify-content-center">
                            <h6> {{text}}</h6>
                        </div>



                        <div class="card-body row justify-content-center">
                            {{wallets}}
                        </div>

                        <div class="row justify-content-center">
                            <h6> {{text1}}</h6>
                        </div>

                          <div class="card-body row justify-content-center">
                            {{totalAmmountMoney}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function(){
        return{
            title: 'Welcome to VIRTUAL WALLET',
            text:'Total Number Of Virtual Wallets: ',
            wallets: undefined,

            text1:'Ammount Of Money In The Platform: ',
            totalAmmountMoney: '',
        }
    },
    methods: {
        getTotalAmmountMoney(){
                axios.get('api/totalAmmountMoney').then(({ data }) => (this.totalAmmountMoney = data));
            },
    },

    created(){
            this.getTotalAmmountMoney();
    },
    mounted(){
        axios.get('api/home')
        .then(response=>{
            this.wallets = response.data;
        });
    },
}
</script>
