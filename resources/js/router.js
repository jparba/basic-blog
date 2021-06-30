import Vue from 'vue';
import VueRouter from 'vue-router';

import Login from './components/Login'
import Register from './components/Register'
import Home from './components/Home'
import MyPost from './components/MyPost'
import SinglePost from './components/SinglePost'
import MyProfile from './components/MyProfile'
import Profile from './components/Profile'
import Notfound from './components/Notfound'

Vue.use(VueRouter);

const router = new VueRouter({
	mode: 'history',
	routes: [
		{
			path: '/',
			component: Login,
			name: 'Login',
		},
		{
			path: '/register',
			component: Register,
			name: 'Register'
		},
		{
			path: '/home',
			component: Home,
			name: 'Home'
		},
		{
			path: '/mypost',
			component: MyPost,
			name: 'MyPost'
		},
		{
			path: '/myprofile',
			component: MyProfile,
			name: 'MyProfile'
		},
		{
			path: '/profile/:name',
			component: Profile,
			name: 'Profile'
		},
		{
			path: '/post/:slug',
			component: SinglePost,
			name: 'SinglePost'
		},
		{
			path: '*',
			component: Notfound,
			name: 'Notfound'
		},
	]
})

router.beforeEach((to, from, next) => {
	let isAuthenticated = false

	if(localStorage.getItem("loggedUser") != null) isAuthenticated = true
	else isAuthenticated = false

	if (to.name == 'Register') return next()

	if(to.name == 'Login' && isAuthenticated) next({name: 'Home'})

	if (to.name !== 'Login' && !isAuthenticated) {
			next({name: 'Login'})
			Vue.$toast.error('Please Login')
	}else {
		next()
	}
})

export default router