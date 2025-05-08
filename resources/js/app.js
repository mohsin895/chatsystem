import './bootstrap';

import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import '../css/app.css';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import VueLazyLoad from 'vue3-lazyload'
import * as icons from 'lucide-vue-next'


import './axios';

const app = createApp(App);



app.use(Toast); 
app.use(router);
app.use(VueLazyLoad);
app.mount('#app');

