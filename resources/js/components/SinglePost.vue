<template>
	<div>
		<Navcomponent></Navcomponent>
		<div v-if="loading" class="the_loader">
			<div class="spinner-border text-primary" style="width: 5rem; height: 5rem;" role="status">
			</div>
		</div>
		<div v-else>
			<div v-if="theSinglePost.user" class="my-4">
				<div class="text-right">
		  		<button
			    	v-if="loggedUser.id === theSinglePost.user.id"
		        type="button"
		        class="btn btn-primary btn-sm mt-2"
		        @click="editPost"
		        data-bs-toggle="modal"
		        data-bs-target="#postModal"
		      >
			      Edit
		      </button>
		      <button
			    	v-if="loggedUser.id === theSinglePost.user.id"
		        type="button"
		        class="btn btn-danger btn-sm mt-2"
		        @click="deletePost"
		      >
		      Delete
		      </button>
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
		                <textarea class="form-control" id="post-content" required v-model="postData.content"></textarea>
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
		            	@click="updatePost"
		            >
		          		{{ busy ? 'Updating...': 'Update'}}
		          		<span v-if="busy" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
		          	</button>
		          </div>
		        </div>
		      </div>
		    </div> <!-- end modal -->

				<h1>{{ theSinglePost.title }}</h1>
				<span class="card-subtitle mb-2 text-muted">Author:
					<router-link :to="`/profile/${theSinglePost.user.name}`">
		    		{{ theSinglePost.user.name }}
		    	</router-link>
		    </span>

		    <hr>

		    <div class="mt-4" v-html="theSinglePost.content"></div>

		    <a id="commentSection" class="btn btn-primary my-4" data-toggle="collapse" href="#comment" role="button" aria-expanded="false" aria-controls="comment">
		        {{ theSinglePost.comment.length >= 1 ? `${theSinglePost.comment.length} comment(s)` : `No comment`}}
		      </a>

		    <!-- comment -->
		    <div class="collapse mt-2 mb-4" id="comment">
		      <div v-for="(comment, index) in theSinglePost.comment" class="card card-body mt-2">
		        {{ comment.content }}
		        <div>
		        	<small>Comment by</small>
		        	<router-link :to="`/profile/${comment.author}`">{{ comment.author }}</router-link>
		        </div>
		        <div v-if="loggedUser.id === comment.user_id" class="comment_list">
		        	<ul>
		        		<li
		        			class="text-success mr-2"
		        			@click="editComment(index)"
		        		>
		        			Edit
		        		</li>
		        		<li
		        			class="text-danger"
		        			@click="deleteComment(index)"
		        		>
		        			Delete
		        		</li>
		        	</ul>
		        </div>
		      </div>

		    </div>

		    <div class="mt-3">
				  <textarea id="commentArea" v-model="commentData" class="form-control" placeholder="Leave a comment..."></textarea>
			  	<button
			  		v-if="editingComment"
			      type="button"
			      class="btn btn-danger btn-sm mt-2"
			      @click="[editingComment = false, commentData = '']"
			    >
			      Cancel
			    </button>
		    	<button
		        type="button"
		        class="btn btn-secondary btn-sm mt-2"
		        @click="saveComment"
		        :disabled="!commentData"
		      >
		        {{ editingComment ? 'Update comment' : 'Save comment'}}
		      </button>
		    </div> <!-- end comment -->
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
				loading: true,
				busy: false,
				editingComment: false,
				postData: {
					title: '',
					content: '',
					slug: '',
				},
				loggedUser: {},
				commentData: '',
				editingId: '',
				editingIndex: ''
			}
		},
		methods: {
			async singlePost(slug) {
				await this.$store.dispatch('getSinglePost', slug)
				.then(() => {
					this.loading = false
					if(Object.keys(this.theSinglePost).length === 0) {
						this.$router.push({name: 'Home'})
					}
				})
			},
			async updatePost() {
				this.busy = true
				await this.$store.dispatch('updatePost', { single: true, id: this.theSinglePost.id, data: this.postData })
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
			},
			async editPost() {
				this.postData.title = this.theSinglePost.title
				this.postData.content = this.theSinglePost.content
				this.postData.slug = this.theSinglePost.slug
			},
			async deletePost() {
				await this.$store.dispatch('deletePost', { id: this.theSinglePost.id, single: true})
			},
			async saveComment() {
				let comment = {
					single: true,
					index: this.editingIndex,
					post_id: this.theSinglePost.id,
					author: this.theSinglePost.user.name,
					content: this.commentData
				}

				await this.$store.dispatch(
					this.editingComment ? 'updateComment' : 'saveComment',
					this.editingComment ?
						{id: this.editingId, commentIndex: this.editingIndex, data: comment } : comment)
					.then(()=> {
						this.commentData = ''
						document.getElementById('comment').classList.add('show');

						this.editingComment ? this.editingComment = false : ''
					})
			},
			editComment(index) {
				this.editingComment = true
				let comment = this.theSinglePost.comment[index]
				this.commentData = comment.content
				this.editingId = comment.id
				this.editingIndex = index
				document.getElementById('commentArea').focus()
			},
			async deleteComment(index) {
				let comment = this.theSinglePost.comment[index]
				await this.$store.dispatch('deleteComment', { index, id: comment.id, single: true})
			},
			toDashes() {
				let title = this.theSinglePost.title.trim()
				this.theSinglePost.slug = title.replace(/\s+/g, '-').toLowerCase()
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
		computed: {
			...mapGetters({
				theSinglePost: 'singlePost',
				errorList: 'errorList'
			})
		},
		mounted() {
			this.singlePost(this.$route.params.slug)
			this.loggedUser = JSON.parse(localStorage.getItem("loggedUser"))
		}
	}
</script>