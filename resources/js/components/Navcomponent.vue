<template>
	<div>
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		  <div class="container-fluid">
		    <router-link to="/" class="navbar-brand">Basic blog</router-link>
		    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDarkDropdown" aria-controls="navbarNavDarkDropdown" aria-expanded="false" aria-label="Toggle navigation">
		      <span class="navbar-toggler-icon"></span>
		    </button>
		    <div class="collapse navbar-collapse" id="navbarNavDarkDropdown">
		      <ul class="navbar-nav">
		        <li class="nav-item dropdown">
		          <a class="nav-link dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
		            {{ theUser.email }}
		          </a>
		          <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
		            <router-link class="dropdown-item" to="/myprofile">My profile</router-link>
		            <router-link class="dropdown-item" to="/mypost">My post</router-link>
		            <li><a class="dropdown-item" href="/logout" @click.prevent="logout">Logout</a></li>
		          </ul>
		        </li>
		      </ul>
		    </div>
		  </div>
		</nav>
	</div>
</template>
<script>
	export default {
		computed: {
			theUser() {
				if (localStorage.getItem("loggedUser") != null) {
				  return JSON.parse(localStorage.getItem("loggedUser"))
				}
			}
		},
		methods: {
			async logout() {
				this.$store.dispatch('logout')
			}
		},
	}
</script>

<style>
	.navbar-collapse{
		flex-grow: 0!important
	}
</style>