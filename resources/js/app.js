/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

import Vuex from 'vuex';
import router from './router';

import dataStore from './store/index';

// vue-cookies
import VueCookies from 'vue-cookies'

// toasted module
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
const options = {
    timeout: 4000
};

Vue.use(Vuex);
Vue.use(Toast, options);
Vue.use(VueCookies);


const app = new Vue({
    el: '#app',
    router: router,
    store: new Vuex.Store(dataStore),
});
