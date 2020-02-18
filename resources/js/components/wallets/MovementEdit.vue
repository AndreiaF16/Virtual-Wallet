<template>
	<div class="jumbotron">
	    <h2>Edit Movement: {{ movement.id }}</h2>
	    <div class="form-group">
	        <label for="selectCategory">Category:</label>
	        <select name="selectCategory" class="form-control" v-model="movement.category">
				<option v-for="categoryType in categories" :key="categoryType.id" v-bind:value="categoryType"
				:selected="movement.category == categoryType">{{ categoryType.name }}</option>
			</select>
	    </div>
	    <div class="form-group">
	        <label for="inputDescription">Description:</label>
	        <input
	            type="text" class="form-control" v-model="movement.description"
	            name="newDescription" id="inputDescription"/>
	    </div>

	    <div class="form-group">
	        <a class="btn btn-success" v-on:click.prevent="saveMovement()">Save</a>
	        <a class="btn btn-danger" v-on:click.prevent="cancelEdit()">Cancel</a>
	    </div>
	</div>
</template>

<script type="text/javascript">
	module.exports={
		props: ['movement'],
		data:
		function() {
			return{
				categories:[]
			}
		},
		methods: {
	        saveMovement: function(){
                axios.put('api/movements/'+this.movement.id, this.movement)
	                .then(response=>{
                        if(response.data == 'Category does not exist for this type of movement'){
                            this.$emit('category-error');
                        }else{
                            this.$emit('save-edit')
                        }
                        //this.$store.commit('setUser', response.data.data);
                    })
                    .catch(error => {
                        console.error(error);
                    })

	        },
	        cancelEdit: function(){
				this.$emit('edit-canceled');
	        }
		},created(){
			if(this.movement.type == "i"){
				axios.get('/api/categories/income')
					.then(response => {
						this.categories = response.data.data;
					});
			}else if(this.movement.type == "e"){
				axios.get('/api/categories/expense')
					.then(response => {
						this.categories = response.data.data;
					});
			}
		}
	}
</script>
