import Vue from 'vue';
import store from '../stores';

export default {
	isLogin() {
		const token = window.localStorage.getItem('token');
		if (token && token.length) {
			Vue.http.defaults.headers.common.Accept = 'application/json';
			Vue.http.defaults.headers.common.Authorization = `Bearer ${token}`;
			store.commit('token', token);

			return true;
		}
		return false;
	},
};