<template>
     <div class="jumbotron">
        <h2>Movement ID: {{ movement.id }}</h2>

        <br>
         <div class="form-group" v-if="movement.transfer_wallet">
            <div class="col-md-10 col-md-offset-1">
                <td><img v-bind:src="movementPhoto" style="width:150px; height:150px; border-radius:50%; margin-bottom:25px; margin-right:25px; float:left;"></td>
            </div>
         </div>

        <div class="form-group">
	        <label v-if="movement.description"> <b>Description: </b>  {{ movement.description}}</label>
	        <label v-if="!movement.description"><b>Description:  </b> No Description, empty field</label>
	    </div>

        <div class="form-group">
	        <label v-if="movement.source_description"><b>Source Description: </b>  {{ movement.source_description}}</label>
	        <label v-if="!movement.source_description"><b>Source Description: </b>   No Source Description, empty field</label>
	    </div>

        <div class="form-group">
	        <label v-if="movement.iban"><b>IBAN:  </b> {{ movement.iban}}</label>
	        <label v-if="!movement.iban"><b> IBAN:</b> No IBAN, Empty field</label>
	    </div>

        <div class="form-group">
	        <label v-if="movement.mb_entity_code"><b>MB Entity Code: </b>  {{ movement.mb_entity_code}}</label>
	        <label v-if="!movement.mb_entity_code"><b>MB Entity Code: </b>  No MB Entity Code, empty field</label>
	    </div>

        <div class="form-group">
	        <label v-if="movement.mb_payment_reference"><b>MB Payment Reference: </b>  {{ movement.mb_payment_reference}}</label>
	        <label v-if="!movement.mb_payment_reference"><b>MB Payment Reference: </b>   No MB Payment Reference, empty field</label>
	    </div>

       <button type="button" class="btn btn-danger" v-on:click.prevent="cancelDetails()">Close</button>
    </div>
</template>

<script>
    export default {
        props: ['movement'],
        data: function(){
			return {
                user: {},
                movementPhoto: null
            };
        },
        methods: {
            cancelDetails: function(){
                this.$emit('details-canceled');
            },
            getPhoto(){
                this.movementPhoto = null;
                if(this.movement.transfer_wallet){
                    axios.get("api/getphotobyemail/"+this.movement.transfer_wallet.email).then(response => {
                        this.movementPhoto = "storage/fotos/" + response.data[0];
                        if(response.data[0] == null){
                            this.movementPhoto = "http://neoleader.com.br/wp-content/uploads/2015/05/geral_adulto-300x300.png";
                        }
                    }).catch(error => {
                        this.movementPhoto = null;
                    });
                }
            }

        },mounted(){
            this.user = JSON.parse(localStorage.getItem('user'));
            this.getPhoto();
        }
    }
</script>
