<template>
<div>

     <table class="table table-striped">
        <thead>
            <tr>
                <th>Origin</th>
                <th>Type</th>
                <th>Transfer</th>
                <th>Destination</th>
                <th>Type Payment</th>
                <th>Category Name</th>
                <th>IBAN</th>
                <th>MB Entity Code</th>
                <th>MB Payment Reference</th>
                <!-- <th>Description</th>
                <th>Source Description</th> -->
               <th>Date</th>
                <th>Start Balance</th>
                <th>End Balance</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="movement in movements" :key="movement.id">
                <td>{{ movement.wallet_id }}</td>
                <td>{{ movement.type }}</td>
                <td>{{ movement.transfer }}</td>
                <td>{{ movement.transfer_wallet_id }}</td>
                <td>{{ movement.type_payment }}</td>
                <td>{{ movement.category_id }}</td>
                <td>{{ movement.iban }}</td>
                <td>{{ movement.mb_entity_code }}</td>
                <td>{{ movement.mb_payment_reference }}</td>
               <td>{{ movement.date }}</td>
                <td>{{ movement.start_balance }}</td>
                <td>{{ movement.end_balance }}</td>
                <td>{{ movement.value }}</td>
            </tr>
        </tbody>
    </table>
    </div>
</template>

<script type="text/javascript">
    import showMessage from '../helpers/showMessage.vue';

    export default {
       props: ['movements'],
        data: function () {
            return{
                title: 'Movements',
                currentUser: null,


            showMessage: false,
            showErrors: false,
            typeofmsg: "",
            message:'',

             columns: [
                {
                    label: "Origin",
                    field: 'wallet_id ',
                }, {
                    label: "Type",
                    field: 'type',
                },{
                    label: "Transfer",
                    field: 'transfer',
                },{
                    label: "Destination",
                    field: 'transfer_wallet_id ',
                },
                {
                    label: "Type Payment",
                    field: 'type_payment ',
                },{
                    label: "Category Namea",
                    field: 'category_id ',
                },{
                    label: "IBAN",
                    field: 'iban ',
                },{
                    label: "MB Entity Code",
                    field: 'mb_entity_code ',
                },{
                    label: "MB Payment Reference",
                    field: 'mb_payment_reference ',
                },{
                    label: "Date",
                    field: 'date ',
                },{
                    label: "Start Balance",
                    field: 'start_balance ',
                },{
                    label: "End Balance",
                    field: 'end_balance ',
                },{
                    label: "Value",
                    field: 'value ',
                },
            ],
        };
    },
        methods: {
             getMovements: function () {
                axios.get('api/movements')
                    .then(response => { this.movements = response.data.data; });
            },
            close(){
            }
        },
        mounted(){
            this.getMovements();
        },
        components: {
            //            'movement-list':moventsListReference,
            'show-message':showMessage,
        },
    }
</script>
<style>
</style>
