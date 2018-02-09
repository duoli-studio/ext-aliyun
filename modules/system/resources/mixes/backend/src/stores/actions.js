import injection from '../helpers/injection';

export const information = ({commit}) => {
	commit('loading', true);
	injection.http.post(`${window.api}backend/system/setting/fetch`, {
		namespace : 'test',
		group     : 'test'
	}).then(response => {
		const settings = [];
		const {data} = response.data;
		Object.keys(data).forEach(key => {
			settings[data[key].key] = data[key].value;
		});
		commit('setting', settings);
	}).catch(() => {
		commit('loading', false);
	});
	// 获取 导航, 页面, 脚本, 样式
	injection.http.post(`${window.api}backend/system/layout/information`).then(response => {
		const {
			navigation, pages, scripts, stylesheets
		} = response.data.data;
		commit('navigation', navigation);
		commit('page', pages);
		commit('script', scripts);
		commit('stylesheet', stylesheets);
		const keys = Object.keys(navigation);
		if (keys.length > 0 && navigation[keys[0]].children) {
			commit('sidebar', navigation[keys[0]].children);
		}
		commit('loading', false);
	}).catch(() => {
		commit('loading', false);
	});
};

export const setting = ({commit}) => (new Promise((resolve, reject) => {
	injection.http.post(`${window.api}backend/system/setting/fetch`, {
		namespace : 'test',
		group     : 'test'
	}).then(response => {
		const settings = [];
		const {data} = response.data;
		Object.keys(data).forEach(key => {
			settings[data[key].key] = data[key].value;
		});
		commit('setting', settings);
		resolve();
	}).catch(() => {
		reject();
	});
}));