<template>
	<div class="login">
		<div class="wrapform">
			<h2 class="mb-4">Login</h2>
			<div class="mb-3">
			  <label class="form-label">Email address</label>
			  <input v-model="credentials.email" type="email" class="form-control" placeholder="name@example.com">
			  <small class="text-danger" v-if="'email' in errorList" v-text="errorList['email'][0]"></small>
			</div>
			<div class="mb-3">
			  <label class="form-label">Password</label>
			  <input v-model="credentials.password" type="password" class="form-control" placeholder="******">
			  <small class="text-danger" v-if="'password' in errorList" v-text="errorList['password'][0]"></small>
			</div>
			<div class="text-right">
				<button
					type="button"
					class="btn btn-danger ml-auto"
					@click="clear"
					:disabled="busy"
					>
					Clear
				</button>
				<button
					type="button"
					class="btn btn-primary"
					@click.prevent="login"
					:disabled="busy"
					>
					Login
					<span v-if="busy" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
				</button>
			</div>
			<small>Don't have an account? <router-link to="register">Register</router-link></small>
		</div>
	</div>
</template>
<script>
import { mapGetters } from 'vuex'
	export default {
			data() {
				return {
					busy: false,
					credentials: {
						email: 'geminix2793@gmail.com',
						password: 'jparba'
					}
				}
			},
			computed: {
				...mapGetters({
					errorList: 'errorList'
				})
			},
			methods: {
				async login() {
					this.busy = true
					await this.$store.dispatch('login', this.credentials)
						.then(() => {
							if(this.$cookies.isKey('token')) {
								setTimeout(() => {
									this.$router.push({name: 'Home'})
								}, 500)
							}
						})
					this.busy = false
				},
				clear() {
				  for(const data in this.credentials) {
				   this.credentials[data] = ''
				  }
			    this.$store.commit('INITIALSTATE', ['error'])
				},
			}
	}
</script>

<style scoped>
.login{
	height: 100vh;
	display: flex;
	justify-content: center;
	align-items: center;
}
.wrapform{
	width: 500px;
	max-width: 100%;
}
</style>