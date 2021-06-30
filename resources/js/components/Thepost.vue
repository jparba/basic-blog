<template>
	<div>
		<Navcomponent></Navcomponent>
		<div v-if="loading" class="the_loader">
			<div class="spinner-border text-primary" style="width: 5rem; height: 5rem;" role="status">
			</div>
		</div>
		<div v-else>
			<button
				type="button"
				id="addPost"
				class="d-block ml-auto mt-3 btn btn-outline-primary btn-sm"
				data-bs-toggle="modal"
				data-bs-target="#postModal"
			>
				Add post
			</button>
				<div v-for="(post, index) in postList.data" class="card mt-3">
				  <div class="card-body">
				  	<div class="d-flex">
					    <h5 class="card-title">{{ post.title }}</h5>
					    <div class="ml-auto">
					  		<button
						    	v-if="loggedUser.id === post.user.id"
					        type="button"
					        class="btn btn-primary btn-sm mt-2"
					        @click="editPost(index)"
					        data-bs-toggle="modal"
					        data-bs-target="#postModal"
					      >
						      Edit
					      </button>
					      <button
						    	v-if="loggedUser.id === post.user.id"
					        type="button"
					        class="btn btn-danger btn-sm mt-2"
					        @click="deletePost(index)"
					      >
					      Delete
					      </button>
					    </div>
				  	</div>
				    <h6 class="card-subtitle mb-2 text-muted">Author:
				    	<router-link :to="`profile/${post.user.name}`">
				    		{{ post.user.name }}
				    	</router-link>
				    </h6>
				    <p class="card-text" v-html="post.content"></p>
				    <a :id="`commentSection${index}`" data-toggle="collapse" :href="`#comment${index}`"  role="button" aria-expanded="false" :aria-controls="`comment${index}`">
				        {{ post.comment.length >= 1 ? `${post.comment.length} comment(s)` : `No comment`}}
				      </a>
				    <router-link :to="`post/${post.slug}`">View post</router-link>

				    <div class="collapse mt-2" :id="`comment${index}`">
				      <div v-for="(comment, commentIndex) in post.comment" class="card card-body mt-2">
				        {{ comment.content }}
				        <div>
				        	<small>Comment by</small>
				        	<router-link :to="`/profile/${comment.author}`">{{ comment.author }}</router-link>
				        </div>
				        <div v-if="loggedUser.id === comment.user_id" class="comment_list">
				        	<ul>
				        		<li
				        			class="text-success mr-2"
				        			@click="editComment(index, commentIndex)"
				        		>
				        			Edit
				        		</li>
				        		<li
				        			class="text-danger"
				        			@click="deleteComment(index, commentIndex)"
				        		>
				        			Delete
				        		</li>
				        	</ul>
				        </div>
				      </div>
				    </div>

				    <div class="mt-3">
						  <textarea :id="`commentArea${index}`" v-model="commentData[index]" class="form-control" placeholder="Leave a comment..."></textarea>
					  	<button
					  		v-if="editingComment && index == currentIndex"
					      type="button"
					      class="btn btn-danger btn-sm mt-2"
					      @click="[editingComment = false, commentData = []]"
					    >
					      Cancel
					    </button>
				    	<button
				        type="button"
				        class="btn btn-secondary btn-sm mt-2"
				        @click="saveComment(index)"
				        :disabled="!commentData[index]"
				      >
				        {{ editingComment && index == currentIndex ? 'Update comment' : 'Save comment'}}
				      </button>
				    </div>
				  </div>
				</div>

			<!-- Modal -->
			<div class="modal fade" id="postModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">

			  <div class="modal-dialog  modal-dialog-centered modal-dialog-scrollable modal-xl">
			    <div class="modal-content">
			      <div class="modal-header">
			        <h5 class="modal-title" id="postModalLabel">Add new post</h5>
			      </div>
			      <div class="modal-body">
			      	<form>
			          <div class="mb-3">
			            <label for="post-title" class="col-form-label">Tilte</label>
			            <input type="text" class="form-control" id="post-title" v-model="postData.title" @change="toDashes">
			            <small class="text-danger" v-if="'title' in errorList" v-text="errorList['title'][0]"></small>
			          </div>
			          <div class="mb-3">
			            <label for="post-content" class="col-form-label">Content</label>

			            <vue-editor id="post-content" v-model="postData.content"></vue-editor>

			            <small class="text-danger" v-if="'content' in errorList" v-text="errorList['content'][0]"></small>
			          </div>
			        </form>
			      </div>
			      <div class="modal-footer">
			        <button
				        type="button"
				        class="btn btn-secondary"
				        id="closeModal"
				        data-bs-dismiss="modal"
				        :disabled="busy"
				        @click.prevent="clear(true)"
				      >
				        Close
				      </button>
			        <button
			        	type="button"
			        	class="btn btn-primary"
			        	:disabled="busy"
			        	@click="savePost"
			        >
			      		{{ busy ? 'Saving...': editing ? 'Update': 'Save'}}
			      		<span v-if="busy" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
			      	</button>
			      </div>
			    </div>
			  </div>
			</div>

		</div><!-- end post -->

	<!-- 	<div id="loadmore" v-if="!loading">
			<Observer @intersect="intersected"/>
		</div> -->

	</div>
</template>
<script>
import { mapGetters } from 'vuex'
import { VueEditor } from "vue2-editor";
import Navcomponent from './Navcomponent'
import Observer from './IntersectionObserver'

	export default {
		components: { Navcomponent, VueEditor, Observer },
		data() {
			return {
				loading: true,
				busy: false,
				editing: false,
				editingComment: false,
				loggedUser: {},
				postData: {
					title: '',
					content: '',
					slug: '',
				},
				commentData: [],
				busyComment: [],

				currentIndex: '',
				editingId: '',
				editingIndex: '',

				page: 1
			}
		},
		props: {
			mypost: {
				type: Boolean,
				default() {
					return false
				}
			}
		},
		computed: {
				...mapGetters({
					postList: 'postList',
					errorList: 'errorList'
				}),
		},
		methods: {
			intersected() {
				this.page++
				if(this.page <= this.postList.last_page) {
					this.fetchPost()
				}

				if(this.page == this.postList.last_page) {
					document.getElementById('loadmore').classList.add('d-none')
				}

			},
			async fetchPost() {
				await this.$store.dispatch('getPost', {page: this.page, mypost:this.mypost})
				.then(() => {
					this.loggedUser = JSON.parse(localStorage.getItem("loggedUser"))
					this.loading = false;
				})
			},
			async savePost() {
				this.busy = true
				await this.$store.dispatch(
						this.editing ? 'updatePost' : 'savePost',
						this.editing ? {index: this.editingIndex, id: this.editingId, data: this.postData }: this.postData)
					.then(() => {
						if(this.errorList.length <= 0) {
							this.clear()
							this.busy = false
							setTimeout(() => {
								document.getElementById('closeModal').click();
							}, 300)
						}
					})
				this.busy = false
				this.editing = false
			},
			async editPost(index) {
				this.editing = true
				let post = this.postList.data[index]
				this.postData.title = post.title
				this.postData.title = post.title
				this.postData.content = post.content
				this.postData.slug = post.slug
				this.editingId = post.id
				this.editingIndex = index
			},
			async deletePost(index) {
				await this.$store.dispatch('deletePost', {index : index, id: this.postList.data[index]['id']})
			},
			async saveComment(index) {
				let comment = {
					index: index,
					post_id: this.postList.data[index].id,
					author: this.postList.data[index].user.name,
					content: this.commentData[index]
				}

				await this.$store.dispatch(
					this.editingComment ? 'updateComment' : 'saveComment',
					this.editingComment ? {id: this.editingId, commentIndex: this.editingIndex, data: comment } : comment)
					.then(()=> {
						this.commentData = []
						document.getElementById(`comment${index}`).classList.add('show');

						this.editingComment ? this.editingComment = false : ''
					})
			},
			editComment(index, commentIndex) {
				this.editingComment = true
				let comment = this.postList.data[index].comment[commentIndex]
				this.commentData[index] = comment.content
				this.editingId = comment.id
				this.editingIndex = commentIndex
				this.currentIndex = index
				document.getElementById(`commentArea${index}`).focus()
			},
			async deleteComment(index, commentIndex) {
				let comment = this.postList.data[index].comment[commentIndex]
				await this.$store.dispatch('deleteComment', { index, commentIndex, id: comment.id})
			},
			toDashes() {
				let title = this.postData.title.trim()
				this.postData.slug = title.replace(/\s+/g, '-').toLowerCase()
			},
			clear(clearError = false) {
			  for(const data in this.postData) {
			   this.postData[data] = ''
			  }
			  if(clearError) {
			  	this.$store.commit('INITIALSTATE', ['error'])
			  }
			},
		},
		mounted() {
			this.fetchPost()
		},
	}
</script>