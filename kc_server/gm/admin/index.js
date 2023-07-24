import Vue from 'vue';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import App from './App.vue';
import store from './store';
import router from './router';
import "./element.scss";
import VueQuillEditor from 'vue-quill-editor'
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'
import './assets/icon/iconfont.css'

Vue.use(VueQuillEditor)
Vue.use(ElementUI);
const app = new Vue({
    store,
    router,
    render: h => h(App)
}).$mount('#app');
