<template>
    <div class="container">
        <div class="jumbotron row justify-content-center">
                <h1>{{tittle}}</h1>
        </div>

        <show-message :class="typeofmsg" :showSuccess="showMessage" :successMessage="message" @close="close"></show-message>

        <error-validation :showErrors="showErrors" :errors="errors" @close="close"></error-validation>

        <div class="form-group">
            <label for="inputEmail">Email To Debit:</label>
            <input
                type="email" class="form-control" v-model="movement.email"
                name="email" id="inputEmail"
                placeholder="Insert email of the account to receive the money" required
                title="Email must be a valid user email" readonly/>
        </div>

        <div class="form-group">
            <label for="inputValue">Value To Debit:</label>
            <input
                type="text" class="form-control" v-model="movement.value"
                name="value" id="inputValue"
                placeholder="Insert value to credit" required
                title="Value needs to be between 0.1 and 5000"/>
        </div>
        
        <div class="form-group">
            <label for="category">Category:</label>
            <select name="category" id="category" class="form-control" v-model="movement.category_id" required>
                <option value='' selected> -- Select the Category -- </option>
                <option v-for="paymentType in paymentTypes" :key="paymentType.id" v-bind:value="paymentType.id">{{ paymentType.name }}</option>
            </select>
        </div>

        <div class="form-group">
            <label for="type_payment">Type Of Payment:</label>
            <select name="type_payment" id="type_payment" class="form-control" v-model="movement.type_payment" required>
                <option value='' selected> -- Select the Type Of Payment -- </option>
                <option value="bt">Bank Transfer</option>
                <option value="mb">MB Payment</option>
            </select>
        </div>

        <div v-if="this.movement.type_payment == 'bt'" >
            <div class="form-group">
                <label for="inputIBAN">IBAN:</label>
                <input
                    type="text" class="form-control" v-model="movement.iban"
                    name="iban" id="inputIBAN"
                    placeholder="Insert IBAN" required
                    title="INAN must be 2 upper letters followed by 23 numbers"/>
            </div>
        </div>

        <div v-if="this.movement.type_payment == 'mb'" >
            <div class="form-group">
                <label for="inputEntity">Entity:</label>
                <input
                    type="number" class="form-control" v-model="movement.mb_entity_code"
                    name="entity" id="inputEntity"
                    placeholder="Insert Entity" required/>
            </div>

            <div class="form-group">
                <label for="inputReference">Reference:</label>
                <input
                    type="number" class="form-control" v-model="movement.mb_payment_reference"
                    name="reference" id="inputReference"
                    placeholder="Insert Reference" required/>
            </div>

            <div class="form-group">
                <label for="inputDescription">Description:</label>
                <input
                    type="text" class="form-control" v-model="movement.description"
                    name="description" id="inputDescription"
                    placeholder="Insert a description" required/>
            </div>
        </div>

        <div class="form-group">
            <label for="inputTransfer">Transfer:</label>
            <select name="transfer" id="transfer" class="form-control" v-model="movement.transfer" required>
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        <div v-if="this.movement.transfer == '1'">
            <div class="form-group">
                <label for="inputSourceEmail">Destination Wallet Email:</label>
                <input
                    type="email" class="form-control" v-model="movement.destination_email"
                    name="destination_email" id="inputDestinationEmail"
                    placeholder="Destination wallet email address"/>
            </div>
            <div class="form-group">
                <label for="inputSourceDescription">Source Description:</label>
                <input
                    type="text" class="form-control" v-model="movement.source_description"
                    name="source_description" id="inputSourceDescription"
                    placeholder="Insert a source description" required/>
            </div>
        </div>

        <div class="form-group">
            <a class="btn btn-success" v-on:click.prevent="createCredit()">Create Credit</a>
            <a class="btn btn-danger" v-on:click.prevent="cancelDebit()">Cancel</a>
        </div>

    </div>
</template>

<script type="text/javascript">
    import errorValidation from '../helpers/showErrors.vue';
    import showMessage from '../helpers/showMessage.vue';
    export default {
        data: function() {
        return {
            tittle: 'Register Debit',
            name: "RegisterDebit",
            typeofmsg: '',
            message:'',
            showErrors: false,
            showMessage: false,
            errors: [],
            user:{},
            movement:{
                email: '',
                type_payment: '',
                value: '',
                category_id: '',
                iban: '',
                source_description: '',
                mb_entity_code: '',
                description: '',
                mb_payment_reference: '',
                destination_email: '',
                transfer: 0
            },
            paymentTypes: []
        }
    },
    methods: {
        createCredit(){
            axios.post('/api/movements/debit',this.movement)
            .then(response => {
                this.$toasted.success("Debit Complete!")
                let msg = "A new Income of "+ this.movement.value + " is added to your account by " + this.$store.state.user.name;
                if(response.data.email != undefined){
                    this.$socket.emit("notifyMovement",msg,{ email:response.data.email, id: response.data.id});
                    this.$socket.emit("serverUpdateVirtualWallet",{email:response.data.email, id: response.data.id})
                }
                this.$router.push('/home');
            }).catch(error => {
                this.showErrors = true;
                this.errors = error.response.data.errors;
            });
        },
        cancelDebit(){
            this.$router.push('/home');
        },
        close(){
            this.showErrors=false;
            this.showMessage=false;
        },
    },
    components: {
        'show-message':showMessage,
        'error-validation':errorValidation,
    },
    mounted(){
        axios.get('/api/categories/expense')
        .then(response => {
            this.paymentTypes = response.data.data;
        });
        this.user = JSON.parse(localStorage.getItem('user'));
        this.movement.email = this.user.email;
    },
   }
</script>

<style scoped>
</style>