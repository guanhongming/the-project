import './bootstrap';
import { createApp } from 'vue/dist/vue.esm-bundler.js'
import App from '../App.vue'
import router from '../router'
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const app=createApp(App)
app.config.devtools = true;
app.use(router)
app.mount('#app')
