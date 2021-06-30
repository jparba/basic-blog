<template>
  <div>
  	<Navcomponent></Navcomponent>
  	<div v-if="loading" class="the_loader">
  		<div class="spinner-border text-primary" style="width: 5rem; height: 5rem;" role="status">
  		</div>
  	</div>
  	<div v-else class="mt-4">
  	<h2 class="mb-4">Profile information</h2>
	    <div class="row gutters-sm">
	      <div class="col-md-4 mb-3">
	        <div class="card">
	          <div class="card-body">
	            <div class="d-flex flex-column align-items-center text-center">
	            	<div class="preview"
	            	    :style="`background-image: url(/${profile.pic ? 'profile_pic/'+profile.pic : 'no-image.jpg'})`"
	            	></div>
	              <div class="mt-3">
	                <h4> {{ profile.name }} </h4>
	              </div>
	            </div>
	          </div>
	        </div>
	      </div>
	      <div class="col-md-8">
	        <div class="card mb-3">
	          <div class="card-body">
	            <div class="row">
	              <div class="col-sm-3">
	                <h6 class="mb-0">Name</h6>
	              </div>
	              <div class="col-sm-9 text-secondary">
	                {{ profile.name }}
	              </div>
	            </div>
	            <hr>
	            <div class="row">
	              <div class="col-sm-3">
	                <h6 class="mb-0">Email</h6>
	              </div>
	              <div class="col-sm-9 text-secondary">
	                {{ profile.email }}
	              </div>
	            </div>
	            <hr>
	            <div class="row">
	              <div class="col-sm-3">
	                <h6 class="mb-0">Phone</h6>
	              </div>
	              <div class="col-sm-9 text-secondary">
	              	{{ profile.phone }}
	              </div>
	            </div>
	            <hr>
	            <div class="row">
	              <div class="col-sm-3">
	                <h6 class="mb-0">Mobile</h6>
	              </div>
	              <div class="col-sm-9 text-secondary">
	                {{ profile.mobile }}
	              </div>
	            </div>
	            <hr>
	            <div class="row">
	              <div class="col-sm-3">
	                <h6 class="mb-0">Address</h6>
	              </div>
	              <div class="col-sm-9 text-secondary">
	                {{ `${profile.address} ${profile.city} ${profile.state} ${profile.zip}` }}
	              </div>
	            </div>
	            <hr>
	          </div>
	        </div>
	      </div>
	    </div>
  	</div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Navcomponent from './Navcomponent'
	export default {
		components: { Navcomponent },
		data() {
			return {
				loading: true
			}
		},
		computed: {
		    ...mapGetters({
		        profile : 'user'
		    })
		},
		methods: {
			async fetchMe(name) {
			    await this.$store.dispatch('getProfile', name)
			    .then(() => {
		    		this.loading = false
			    	if(Object.keys(this.profile).length === 0) {
			    		this.$router.push({name: 'Home'})
			    	}
			    })
			}
		},
		async mounted() {
			await this.fetchMe(this.$route.params.name)
		}
	}
</script>

<style scoped>
	.preview{
		height: 230px;
	}
</style>