import Vue from 'vue';
import iView from 'iview';
import 'iview/dist/styles/iview.css';
import './assets/less/main.less';
import App from './App.vue';
import injection from './helpers/injection';
import store from './stores';


Vue.config.productionTip = false;
Vue.use(injection);

Vue.use(iView);

injection.instance = new Vue({
	el         : '#app',
	router     : injection.router,
	store,
	template   : '<App/>',
	components : {
		App
	}
});