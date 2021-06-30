<template>
    <div>
        <Navcomponent></Navcomponent>
        <Register :options="options" :profile="profile"></Register>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Navcomponent from './Navcomponent'
import Register from './Register'
    export default {
        components: { Register, Navcomponent },
        data() {
            return {
                options: {
                    'profile': true,
                    'formTitle' : 'Update profile'
                },
                profile: {}
            }
        },
        computed: {
            ...mapGetters({
                user: 'user'
            })
        },
        methods: {
            async fetchMe() {
                await this.$store.dispatch('getMe')
                    .then(() => {
                        this.profile = this.user
                    })
            }
        },
        mounted() {
            this.fetchMe()
        }
    }
</script>