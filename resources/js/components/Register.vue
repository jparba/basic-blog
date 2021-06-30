<template>
    <div>
        <div class="mt-4">
            <h2 class="mb-4">{{ options.formTitle }}</h2>
            <div class="row">
                <div class="col-md-3">
                    <div id="preview" class="preview"
                    :style="`background-image: url(/${userData.pic ? 'profile_pic/'+userData.pic : 'no-image.jpg'})`"
                    ></div>
                    <input type="file" ref="profilepic" id="profilePic" @change="previewImage" class="form-control d-none">
                    <div class="text-center mt-2">
                        <button
                            type="button"
                            class="btn btn-success"
                            @click="openUploader"
                            >
                                {{ hasImage? 'Change image' : 'Choose image' }}
                        </button>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                              <label class="form-label">Name</label>
                              <input type="text" v-model="userData.name" class="form-control" placeholder="Name here">
                              <small class="text-danger" v-if="'name' in errorList" v-text="errorList['name'][0]"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                              <label class="form-label">Email address</label>
                              <input type="email" v-model="userData.email" class="form-control" placeholder="name@example.com">
                              <small class="text-danger" v-if="'email' in errorList" v-text="errorList['email'][0]"></small>
                            </div>
                        </div>
                        <div v-if="!options.profile" class="col-md-6">
                            <div class="mb-3">
                              <label class="form-label">Password</label>
                              <input type="password" v-model="userData.password" class="form-control" placeholder="******">
                              <small class="text-danger" v-if="'password' in errorList" v-text="errorList['password'][0]"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                              <label class="form-label">Phone Number</label>
                              <input type="text" v-model="userData.phone" class="form-control" placeholder="00000000">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                              <label class="form-label">Mobile Number</label>
                              <input type="text" v-model="userData.mobile" class="form-control" placeholder="+639278363198">
                              <small class="text-danger" v-if="'mobile' in errorList" v-text="errorList['mobile'][0]"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                              <label class="form-label">Address</label>
                              <input type="text" v-model="userData.address" class="form-control" placeholder="Address">
                              <small class="text-danger" v-if="'address' in errorList" v-text="errorList['address'][0]"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                              <label class="form-label">City</label>
                              <input type="text" v-model="userData.city" class="form-control" placeholder="City">
                              <small class="text-danger" v-if="'city' in errorList" v-text="errorList['city'][0]"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                              <label class="form-label">State</label>
                              <input type="text" v-model="userData.state" class="form-control" placeholder="State">
                              <small class="text-danger" v-if="'state' in errorList" v-text="errorList['state'][0]"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                              <label class="form-label">Zip</label>
                              <input type="text" v-model="userData.zip" class="form-control" placeholder="Zip">
                              <small class="text-danger" v-if="'zip' in errorList" v-text="errorList['zip'][0]"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <button
                    v-if="!options.profile"
                    type="button"
                    class="btn btn-danger ml-auto"
                    @click="clear(true)"
                    :disabled="busy"
                    >
                    Clear
                </button>
                <button
                    type="button"
                    class="btn btn-primary"
                    @click="register(options.profile)"
                    :disabled="busy"
                    >
                    {{ options.profile ? 'Update' : 'Register' }}
                </button>
            </div>
            <small>Already have an account? <router-link to="/">Login</router-link></small>
        </div>
    </div>
</template>
<script>
import { mapGetters } from 'vuex'

    export default {
        data() {
            return {
                busy: false,
                hasImage: false,
                userData: {
                    name: '',
                    email: '',
                    password: '',
                    phone: '',
                    mobile: '',
                    address: '',
                    city: '',
                    state: '',
                    zip: '',
                },
            }
        },
        props: {
            options: {
                type: Object,
                default() {
                    return {
                        'formTitle' : 'Registration'
                    }
                }
            },
            profile: {
                type: Object,
                default() {
                    return {}
                }
            }
        },
        computed: {
                ...mapGetters({
                    errorList: 'errorList'
                })
        },
        methods: {
            openUploader() {
                this.$refs.profilepic.click()
            },
            previewImage(e) {
                if(e.target.files.length > 0) {
                    this.hasImage = true
                    let reader = new FileReader()

                    reader.onload = function (e) {
              document.getElementById('preview').style.backgroundImage = `url(${e.target.result})`
            }

            reader.readAsDataURL(e.target.files[0]);
                }else {
                    this.hasImage = false
                }
            },
            clear(clearError = false) {
              for(const data in this.userData) {
               this.userData[data] = ''
              }
              if(clearError) {
                this.$store.commit('INITIALSTATE', ['error'])
              }
            },
            async register(update = false) {
                this.busy = true
                const formData = new FormData()
                let imageFile = document.getElementById('profilePic').files

                for(const data in this.userData) {
                  formData.append(data, this.userData[data])
                }

                if(imageFile.length != 0) {
                  formData.append('file', document.getElementById('profilePic').files[0])
                }

                // check if profile  append request
                if(this.options.profile) {
                    formData.append('pic', this.userData.pic)
                }

                await this.$store.dispatch(update ? 'updateME' : 'register', formData)

                this.busy = false
                if(this.errorList.length <= 0 && !this.options.profile) {
                    this.clear()
                    this.$router.push({name: 'Home'})
                }
            }
        },
        watch: {
            profile: {
                handler(newValue, oldValue) {
                    this.userData = newValue
                },
                deep: true
            }
        }
    }
</script>

<style>
    .preview{
        width: 100%;
        max-width: 300px;
        height: 300px;
        background-color: #333;
        background-size: cover;
        background-position: center;
    }
</style>