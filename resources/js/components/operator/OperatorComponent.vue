<template>


<div>
   <div class="container">
      <div class="jumbotron row justify-content-center">
        <h1>{{tittle}}</h1>
      </div>

      <error-validation :showErrors="showErrors" :errors="errors" @close="close"></error-validation>

      </div>
      <div class="form-group">
        <label for="inputEmail">Email</label>
        <input type="email" class="form-control"  v-model="movement.email"  name="email"
          id="inputEmail" placeholder="Enter email address">
      </div>

      <div class="form-group">
        <label for="inputValue">Value</label>
        <input type="text" class="form-control" v-model="movement.value"  name="value" id="inputValue"
        placeholder="Enter value">
      </div>
    <div class="form-group">
        <label for="inputPaymentType">Payment Type</label>
        <select class="form-control" name="PaymentType" id="PaymentType" v-model="movement.type_payment" required>
            <option value='' selected> -- Select the Type Of Payment -- </option>
            <option value="c">Cash</option>
            <option value="bt">Bank Transfer</option>
        </select>
        <br>
        <br>
        <br>
    </div>
    <div v-if="this.movement.type_payment == 'bt'" >

      <div class="form-group">
        <label for="inputSrc_Desc">Source Description</label>
        <textarea type="text" class="form-control" v-model="movement.source_description" name="Src_Desc" id="inputSrc_Desc"
          placeholder="Enter the Source Description">
        </textarea>
      </div>

        <div class="form-group">
            <label for="inputIBAN">IBAN</label>
            <input type="text" class="form-control" v-model="movement.iban" name="IBAN" id="inputIBAN"
            placeholder="Enter IBAN">
        </div>
</div>
        <div class="form-group">
            <a class="btn btn-primary" v-on:click.prevent="registerIncome">Create Income</a>
        </div>

</div>
</template>

<script>
  import errorValidation from '../helpers/showErrors.vue';
  export default{
  data: function() {
    return {
        tittle: 'Create an Income',
      errors: [],
      showMessage: false,
      showErrors: false,
      message:"",
      movement: {
        email: "",
        value: "",
        type_payment: "",
        iban: "",
        source_description: "",
      },
      notificationMsg: ""
    };
  },
  methods: {
    registerIncome() {
      axios.post("api/operator/registerIncome",this.movement)
      .then(response=>{
          this.$toasted.success("Income registered with success!")
          let msg = "A new income of "+ this.movement.value + " is added to your account";
          this.$socket.emit("notifyMovement",msg,{ email:response.data.email, id: response.data.id})
          this.$socket.emit("serverUpdateVirtualWallet",{ email:response.data.email, id: response.data.id})
					this.$router.push('/home');
				}).catch(error=>{
          this.showErrors = true;
          this.errors = error.response.data.errors;
				});
    },
    close(){
        this.showErrors=false;
        this.showMessage=false;
    },
  },
  components: {
      'error-validation':errorValidation,
  },
  sockets:{
    notifyMovement: function(msg){
      this.notificationMsg = msg;
    }
  },
};
</script>

<style>
</style>
