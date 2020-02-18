<template>
    <div>
    <div class="container">
      <div class="jumbotron row justify-content-center">
        <h1>{{tittle}}</h1>
      </div>


      </div>

        <div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Total Users</th>
                        <th>Total Administrators</th>
                        <th>Total Operators</th>
                        <th>Total Platform Users</th>
                        <th>Total Movements</th>
                        <th>Total Active Users</th>
                        <th>Total Transactions</th>
                        <th>Ammount Of Money In The Platform</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{totalUsers}}</td>
                        <td>{{totalAdmins}}</td>
                        <td>{{totalOperators}}</td>
                        <td>{{totalPlatformUsers}}</td>
                        <td>{{totalMovements}}</td>
                        <td>{{totalActiveUsers}}</td>
                        <td>{{totalTransactions}}</td>
                        <td>{{totalAmmountMoney}} €</td>
                    </tr>
                </tbody>
            </table>
        </div>

            <br>

            <div v-if="loadedExternalIncomeMonth" class="container">
                <h4>External Income Per Month:</h4>
                <line-chart :data="data2" :labels="label2" :color="'#800000'"/>
            </div>

            <br>

            <div v-if="loadedInternalTransfersMonth" class="container">
                <h4>Internal Transfers (Expenses) Per Month:</h4>
                <line-chart :data="data3" :labels="label3" :color="'#800000'"/>
            </div>

            <br>

            <div v-if="loadedUsersRegisteredMonth" class="container">
                <h4>Users Registered Per Month:</h4>
                <line-chart :data="data4" :labels="label4" :color="'#800000'"/>
            </div>
        </div>
</template>
<script>
 import LineChart from './LineChart.vue';
 import { Line } from 'vue-chartjs';
export default {
    extends: Line,
    data: function () {
			return {
                tittle: 'Statistics',
                totalUsers: "",
                totalAdmins: "",
                totalOperators: "",
                totalPlatformUsers: "",
                totalMovements: "",
                totalActiveUsers: "",
                totalTransactions: "",
                totalAmmountMoney: "",
                label1:null,
                data1: null,
                label2:null,
                data2: null,
                label3:null,
                data3: null,
                label4:null,
                data4: null,
                loadedMovementsMonth: false,
                loadedExternalIncomeMonth: false,
                loadedInternalTransfersMonth: false,
                loadedUsersRegisteredMonth: false,

			}
		},
        methods: {
            getTotalUsers(){
                axios.get("api/totalUsers").then(({ data }) => (this.totalUsers = data));
            },
            getTotalAdmins(){
                axios.get("api/totalAdmins").then(({ data }) => (this.totalAdmins = data));
            },
            getTotalOperators(){
                axios.get("api/totalOperators").then(({ data }) => (this.totalOperators = data));
            },
            getTotalPlatformUsers(){
                axios.get("api/totalPlatformUsers").then(({ data }) => (this.totalPlatformUsers = data));
            },
            getTotalMovements(){
                axios.get("api/totalMovements").then(({ data }) => (this.totalMovements = data));
            },
            getNumberActiveIUsers(){
            axios.get('api/admin/stats/numberActiveUsers')
            .then(({ data }) => (this.totalActiveUsers = data));
            },
            getTotalTransactions(){
                axios.get('api/totalTransactions').then(({ data }) => (this.totalTransactions = data));
            },
            getTotalAmmountMoney(){
                axios.get('api/totalAmmountMoney').then(({ data }) => (this.totalAmmountMoney = data));
            },
        getExternalIncomeThoughTime(){
            axios.get('api/externalIncomeThroughTime')
            .then( response => {
                this.label2 = response.data.labels;
                this.data2 = response.data.rows;
                this.loadedExternalIncomeMonth = true;
            })

        },
        getInternalTransfersThoughTime(){
            axios.get('api/internalTransfersThroughTime')
            .then( response => {
                this.label3 = response.data.labels;
                this.data3 = response.data.rows;
                this.loadedInternalTransfersMonth = true;
            })

        },
        getUsersRegisteredThroughTime(){
            axios.get('api/usersRegisteredThroughTime')
            .then( response => {
                this.label4 = response.data.labels;
                this.data4 = response.data.rows;
                this.loadedUsersRegisteredMonth = true;
            })

        },


        },
        created(){
            this.getTotalUsers();
            this.getTotalAdmins();
            this.getTotalOperators();
            this.getTotalPlatformUsers();
            this.getTotalMovements();
            this.getNumberActiveIUsers();
            this.getTotalTransactions();
            this.getTotalAmmountMoney();
            this.getExternalIncomeThoughTime();
            this.getInternalTransfersThoughTime();
            this.getUsersRegisteredThroughTime();
        },
        components: {
        'line-chart': LineChart,
    },
    }
</script>
