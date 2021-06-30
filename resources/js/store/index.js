import Vue from "vue";
import axios from "axios";
import cookie from 'vue-cookies'
import router from '../router'

let api = axios.create({
  withCredentials: true,
  baseURL: 'http://localhost:8000/api/v1/',
})

 // Set the AUTH token for api request
 api.interceptors.request.use(function (config) {
   const token = cookie.get('token')
   config.headers.Authorization =  token ? `Bearer ${token}` : '';
   return config;
 });

export default {

	state: {
      user : [],
      post : [],
      error : [],
      singlePost : [],
   },

	getters: {
		user: state => state.user,
		errorList: state => state.error,
		postList: state => state.post,
		singlePost: state => state.singlePost,
	},

	actions: {
		async login({commit}, payload) {
			await axios
					.post('http://localhost:8000/api/v1/login', payload)
					.then(res => {
						if(res.data.success) {
							localStorage.setItem('loggedUser', JSON.stringify(res.data[0]))
							Vue.$cookies.set('token', res.data.token)
							Vue.$toast.success(res.data.message)
						}else{
							Vue.$toast.error(res.data.message)
						}
					}).catch(error => {
						let errors = Object.assign({}, error);
						if(errors.response.status == 422) {
							commit('SETVALIDATIONERROR', errors.response.data.errors)
							console.log('Validation error unprocessable entity')
							Vue.$toast.error(errors.response.data.message)
						}
					})
		},
		async register({commit}, payload) {
			await axios
				.post('http://localhost:8000/api/v1/register', payload)
				.then(res => {
					commit('INITIALSTATE', ['error'])
					if(res.data.success) {
						localStorage.setItem('loggedUser', JSON.stringify(res.data.user))
						Vue.$cookies.set('token', res.data.user.token)
						Vue.$toast.success(res.data.message)
					}else{
						Vue.$toast.error(res.data.message)
					}
				}).catch(error => {
					let errors = Object.assign({}, error);
					if(errors.response.status == 422) {
						commit('SETVALIDATIONERROR', errors.response.data.errors)
						console.log('Validation error unprocessable entity')
						Vue.$toast.error(errors.response.data.message)
					}
				})
		},
		async logout() {
			await api
				.post('logout')
				.then((res) => {
					Vue.$cookies.remove('token')
					localStorage.removeItem('loggedUser')
					router.push({name: 'Login'})
					Vue.$toast.success(res.data.message)
				})
		},
		async getMe({commit}) {
			await api
				.get('profile')
				.then(res => {
					commit('SETUSER', res.data)
				})
		},
		async getProfile({commit}, payload) {
			await api
				.get(`profile/${payload}`)
				.then(res => {
					commit('SETUSER', res.data)
				})
		},
		async updateME({commit}, payload) {
			await api
				.post('profile', payload)
				.then(res => {
					if(res.data.success) {
						Vue.$toast.success(res.data.message)
					}else{
						Vue.$toast.error(res.data.message)
					}
				})
		},
		async getPost({commit}, payload) {
			await api
				.get(`post?page=${payload.page}&&mypost=${payload.mypost}`)
				.then(res => {
					commit('SETPOST', {data: res.data, page: payload.page})
				})
		},
		async getSinglePost({commit}, payload) {
			await api
				.get(`post/${payload}`)
				.then(res => {
					commit('SETSINGLEPOST', res.data)
				})
		},
		async savePost({commit}, payload) {
			await api
				.post('post', payload)
				.then(res => {
					commit('INITIALSTATE', ['error'])
					if(res.data.success) {
						commit('UNSHIFTPOSTDATA', res.data.post)
						Vue.$toast.success(res.data.message)
					}
				}).catch(error => {
					let errors = Object.assign({}, error);
					if(errors.response.status == 422) {
						commit('SETVALIDATIONERROR', errors.response.data.errors)
						console.log('Validation error unprocessable entity')
						Vue.$toast.error(errors.response.data.message)
					}
				})
		},
		async updatePost({commit}, payload) {
			await api
				.patch(`post/${payload.id}`, payload.data)
				.then(res => {
					commit('INITIALSTATE', ['error'])
					if(res.data.success) {
						payload.single ?
							commit('UPDATEPOSTDATA', { data: res.data, single: payload.single}) :
							commit('UPDATEPOSTDATA', [res.data, payload.index])
						Vue.$toast.success(res.data.message)
					}else{
						Vue.$toast.error(res.data.message)
					}
				}).catch(error => {
					let errors = Object.assign({}, error);
					if(errors.response.status == 422) {
						commit('SETVALIDATIONERROR', errors.response.data.errors)
						console.log('Validation error unprocessable entity')
						Vue.$toast.error(errors.response.data.message)
					}
				})
		},
		async deletePost({commit}, payload) {
			await api
				.delete(`post/${payload.id}`)
				.then(res => {
					if(res.data.success) {
						payload.single ?
							router.push({name: 'Home'}) : commit('DELETEPOSTDATA', payload.index)
						Vue.$toast.success(res.data.message)
					}else{
						Vue.$toast.error('Deleting failed.')
					}
				})
		},
		async saveComment({commit}, payload) {
			await api
				.post('comment', payload)
				.then(res => {
					if(res.data.success) {
						commit('PUSHCOMMENTDATA', {
							index: payload.index,
							comment: res.data.comment,
							single: payload.single? payload.single : false
						})
					}
				})
		},
		async updateComment({commit}, payload) {
			await api
				.patch(`comment/${payload.id}`, payload.data)
				.then(res => {
					if(res.data.success) {
						commit('UPDATECOMMENTDATA', {
							commentIndex: payload.commentIndex,
							postIndex: payload.data.index,
							comment: res.data.comment,
							single: payload.data.single? payload.data.single : false
						})
					}
				})
		},
		async deleteComment({commit}, payload) {
			await api
				.delete(`comment/${payload.id}`)
				.then(res => {
					if(res.data.success) {
						commit('DELETECOMMENTDATA', payload)
					}
				})
		}
	},

	mutations: {
		SETUSER: (state, payload) => state.user = payload,
		SETVALIDATIONERROR: (state, payload) => state.error = payload,
		INITIALSTATE: (state, initialState) => {
			initialState.forEach(item => {
				state[item] = []
			})
		},
		SETPOST: (state, payload) => {
			payload.page > 1 ?
			state.post.data.push(payload.data.data) :
			state.post = payload.data
		},
		SETSINGLEPOST: (state, payload) => state.singlePost = payload,
		UNSHIFTPOSTDATA: (state, payload) => state.post.data.unshift(payload),
		UPDATEPOSTDATA: (state, payload) => {
			if(payload.single) {
				state.singlePost.title = payload.data.post.title
				state.singlePost.content = payload.data.post.content
				state.singlePost.slug = payload.data.post.slug
			}else{
				state.post.data[payload[1]]['title'] = payload[0]['post']['title']
				state.post.data[payload[1]]['content'] = payload[0]['post']['content']
				state.post.data[payload[1]]['slug'] = payload[0]['post']['slug']
			}
		},
		DELETEPOSTDATA: (state, payload) => {
			let post = []
			state.post.data.findIndex((value, index) => {
				if(index != payload) {
					post.push(value)
				}
			})

			state.post.data = post
		},
		PUSHCOMMENTDATA: (state, payload) => {
			payload.single ?
				state.singlePost.comment.push(payload.comment) :
				state.post.data[payload.index].comment.push(payload.comment)
		},
		UPDATECOMMENTDATA: (state, payload) => {
			payload.single ?
			state.singlePost.comment[payload.commentIndex] = payload.comment :
			state.post.data[payload.postIndex].comment[payload.commentIndex] = payload.comment
		},
		DELETECOMMENTDATA: (state, payload) => {
				let comment = []
				if(payload.single) {
					state.singlePost.comment.findIndex((value, index) => {
						if(index != payload.index) {
							comment.push(value)
						}
					})
					state.singlePost.comment = comment
				}else{
					state.post.data[payload.index].comment.findIndex((value, index) => {
						if(index != payload.commentIndex) {
							comment.push(value)
						}
					})
					state.post.data[payload.index].comment = comment
				}
		},
	}
}