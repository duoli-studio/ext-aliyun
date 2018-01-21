import axios from 'axios';
// import qs from 'qs';

export default function(injection, Vue) {
	axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

	axios.interceptors.request.use(configuration => configuration, error => Promise.reject(error));

	/*
	axios.interceptors.request.use(configuration => {
		// configuration.headers.Accept = 'application/json, text/plain, * / *';
		// configuration.headers['Content-Type'] = 'application/x-www-form-urlencoded';
		configuration.headers['Content-Type'] = 'application/json';
		if (configuration.method === 'post') {
			configuration.data = qs.stringify({
				...configuration.data
			});
		}
		return configuration;
	}, error => Promise.reject(error));
	*/

	axios.interceptors.response.use(response => response, error => {
		window.console.log(error.response);
		window.console.log(error.response.data);
		if (error.response.status === 401) {
			injection.notice.error({
				title : '请重新登录！',
			});
			injection.router.push({
				path : '/login'
			});
		}
		if (error.response.status > 401 && error.response.status <= 500) {
			if (typeof error.response.data.message === 'object') {
				for (const p in error.response.data.message) {
					injection.notice.error({
						title : error.response.data.message[p],
					});
				}
			}
			else {
				injection.notice.error({
					title : error.response.data.message,
				});
			}
		}
		const dispatch = new Error('Error');
		dispatch.response = error.response;
		throw dispatch;
	});
	Object.defineProperties(injection, {
		http : {
			get() {
				return axios;
			},
		},
	});
	Object.defineProperties(Vue, {
		http : {
			get() {
				return axios;
			},
		},
	});
	Object.defineProperties(Vue.prototype, {
		$http : {
			get() {
				return axios;
			},
		},
	});
}