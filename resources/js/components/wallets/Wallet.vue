<template>
    <div>
        <div class="jumbotron row justify-content-center">
            <h1>{{tittle}}</h1>
        </div>

        <show-message :class="typeofmsg" :showSuccess="showMessage" :successMessage="message" @close="close"></show-message>
        <error-validation :showErrors="showErrors" :errors="errors" @close="close"></error-validation>

        <div class="form-group">
            <label for="inputBalance">Balance of My Virtual Wallet</label>
            <input type="text" class="form-control"
                    name="balance" id="inputBalance"
                    placeholder="Balance" v-model="wallet.balance" readonly/>
        </div>
        <div class="form-group">
            <label for="inputEmail">My Email</label>
            <input type="text" class="form-control"
                    name="email" id="inputEmail"
                    placeholder="Email" v-model="wallet.email" readonly/>
        </div>


 <div class="row justify-content-right">
            <h5>Search Options: </h5>
        </div>
        <div class="row ">

            <div class="col-md-3">

                <div class="form-group">

                    <input type="text" name="id" class="form-control"  placeholder="Search by movement ID" v-model="search.id">
                </div>
                <div class="form-group">
                    <select name="type" class="form-control" v-model="search.type">
                        <option value='' selected> -- Type Of Movement -- </option>
                        <option value="e" >Expense</option>
                        <option value="i" >Income</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <select name="type" class="form-control" v-model="search.category">
                        <option value='' selected> -- Type Of Category -- </option>
                        <option v-for="categoryType in categoryTypes" :key="categoryType.id" v-bind:value="categoryType.id">{{ categoryType.name }}</option>
                    </select>


                </div>
                <div class="form-group">
                    <select name="type_payment" class="form-control" v-model="search.type_payment">
                        <option value='' selected> -- Type Of Payment -- </option>
                        <option value="c" >Cash</option>
                        <option value="bt" >Bank Transfer</option>
                        <option value="mb" >MB Payment</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <input type="text" name="transfer_email" class="form-control" placeholder="Search By Transfer e-mail" v-model="search.transfer_email">
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <input type="text" name="data_sup" class="form-control" placeholder="Date Superior To (yyyy-mm-dd)" v-model="search.data_sup">
                </div>
                <div class="form-group">
                    <input type="text" name="data_inf" class="form-control" placeholder="Date Inferior To (yyyy-mm-dd)" v-model="search.data_inf">
                </div>
            </div>
            <span class="form-group-btn">
                <button type="submit" class="btn btn-primary" v-on:click="getFilteredMovements()">Search</button>
            </span>
        </div>

        <div class="alert alert-danger" v-if="showError">
			<button type="button" class="close-btn" v-on:click="showError=false">&times;</button>
			<strong>{{ successMessage }}</strong>
		</div>

        <div class="alert alert-success" v-if="showSuccess">
			<button type="button" class="close-btn" v-on:click="showSuccess=false">&times;</button>
			<strong>{{ successMessage }}</strong>
		</div>
        <div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Type</th>
                        <th>Transfer E-mail</th>
                        <th>Type Of Payment</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Start Balance</th>
                        <th>End Balance</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="movement in movements.data"  :key="movement.id" :class="{activerow: selectedMovement === movement || selectedMovementEdit === movement}">
                        <td>{{ movement.id }}</td>
                        <td v-if="movement.type == 'e'">Expense</td>
                        <td v-if="movement.type == 'i'">Income</td>


                        <td v-if="movement.transfer_wallet_id != undefined">{{ movement.transfer_wallet.email }}</td>
                        <td v-if="movement.transfer_wallet_id == null">---</td>

                        <td v-if="movement.type_payment == 'c'">Cash</td>
                        <td v-if="movement.type_payment == 'bt'">Bank Transfer</td>
                        <td v-if="movement.type_payment == 'mb'">MB Payment</td>
                        <td v-if="movement.type_payment == null">---</td>


                        <td v-if="movement.category">{{ movement.category.name }}</td>
                        <td v-if="movement.category == null">---</td>


                        <td>{{ movement.date }}</td>
                        <td>{{ movement.start_balance }}</td>
                        <td>{{ movement.end_balance }}</td>
                        <td>{{ movement.value }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary" v-on:click="movementDetais(movement)">Details</button>
                            <button type="button" class="btn btn-sm btn-success" v-on:click="movementEdit(movement)">Edit</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            <pagination :data="movements" :limit=4 @pagination-change-page="getResults"></pagination>
        </div>

        <div class="alert alert-danger" v-if="showErrorEdit">
			<button type="button" class="close-btn" v-on:click="showErrorEdit=false">&times;</button>
			<strong>{{ errorMessageEdit }}</strong>
		</div>

        <movement-details :movement="selectedMovement" @details-canceled="cancelMovementDetails" v-if="selectedMovement"></movement-details>
        <movement-edit :movement="selectedMovementEdit" @edit-canceled="cancelMovementEdit" @save-edit="saveMovementEdit"
        @category-error="showCategoryError" v-if="selectedMovementEdit"> </movement-edit>
    </div>


</template>

<script type="text/javascript">
    import errorValidation from '../helpers/showErrors.vue';
    import showMessage from '../helpers/showMessage.vue';

    import MovementDetailsComponent from "./movementDetails.vue";
    import MovementEditComponent from "./MovementEdit.vue";

    export default {
        data:
        function() {
            return{
                pagination: [],
                tittle: "My Virtual Wallet",
                user: {},
                wallet: {},
                errors: [],
                movements: [],
                showMessage:false,
                showErrors: false,
                typeofmsg: "",
                message:'',
                message:'',
                typeofmsg: "",
                current_page: 1,
                rows: [],
                photo:'',
                movements: {},
                selectedMovement: null,
                selectedMovementEdit: null,
                balance: "",
                search:{
                    user_id: '',
                    id: '',
                    type: '',
                    category: '',
                    type_payment: '',
                    transfer_email: '',
                    data_inf: '',
                    data_sup: '',
                },
                showError: false,
                showSuccess: false,
                successMessage: '',
                errorMessageEdit: '',
                showErrorEdit: false,
                categoryTypes: []

            }
        },

        methods: {
            getFilteredMovements: function(){
                axios.post('api/movements/filter', this.search)
                    .then(response=>{
                        if(response.data == 'E-mail does not exist!'){
                            this.showError = true;
                            this.successMessage = response.data;
                        }else if(response.data == 'Category does not exist!'){
                            this.showError = true;
                            this.successMessage = response.data;
                        }else{
                            this.movements = response.data;
                        }
                    })
                    .catch(error => {
                        console.log(error);
                    })
            },
            getResults:function(page = 1){
                axios.post('api/movements/filter?page=' + page, this.search)
                    .then(response=>{
                        this.movements = response.data;
                    })
            },
            movementDetais: function(movement){
                this.selectedMovementEdit = null;
                this.selectedMovement = movement;
            },
            cancelMovementDetails: function(){
                this.selectedMovement = null;
            },
            movementEdit: function(movement){
                this.selectedMovement = null;
                this.selectedMovementEdit = movement;
            },
            cancelMovementEdit: function(){
                this.selectedMovementEdit = null;
            },
            saveMovementEdit: function(){
                this.selectedMovementEdit = null;
                this.showSuccess = true;
                this.successMessage = 'Movement edit successfully!'
                this.$toasted.success(this.successMessage);

            },
            showCategoryError: function(){
                this.showErrorEdit = true;
                this.errorMessageEdit = 'Category does not exist for this type of movement';
            },
            close(){
                this.showErrors=false;
                this.showMessage=false;
            },
        },

        components: {
            "movement-details": MovementDetailsComponent,
            "movement-edit": MovementEditComponent,
            'show-message':showMessage,
            'error-validation':errorValidation,

        },

        mounted() {
            this.user = JSON.parse(localStorage.getItem('user'));

            this.search.user_id = this.user.id;

            this.getFilteredMovements();

            axios.get('/api/users/me')
            .then(response => {
                this.wallet = response.data.data.wallet;
            });

            axios.get('/api/categories')
            .then(response => {
                this.categoryTypes = response.data.data;
            });
        },
        sockets:{
            updateVirtualWallet: function(){
                this.user = JSON.parse(localStorage.getItem('user'));

                this.search.user_id = this.user.id;

                this.getFilteredMovements();

                axios.get('/api/users/me')
                .then(response => {
                    this.wallet = response.data.data.wallet;
                });

                axios.get('/api/categories')
                .then(response => {
                    this.categoryTypes = response.data.data;
                });
            }
        }
    }
</script>
<style scoped>
tr.activerow {
  background: #09090a !important;
  color: #fff !important;
}
</style>
