import injection from '../helpers/injection';

export const information = ({commit}) => {
	commit('loading', true);
	injection.http.post(`${window.api}/system/graphql`, {
		query : 'query {  config(module : "system", group : "setting") {\n' +
		'    item,\n' +
		'    value\n' +
		'}}'
	}).then(response => {
		const settings = [];
		Object.keys(response.data.data.config).forEach(key => {
			settings[response.data.data.config[key].item] = response.data.data.config[key].value;
		});
		commit('setting', settings);
	});
	injection.http.post(`${window.api}/system/information`).then(response => {
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
	injection.http.post(`${window.api}/administration`, {
		query : 'query {settings{key,value}}',
	}).then(response => {
		const settings = [];
		Object.keys(response.data.data.settings).forEach(key => {
			settings[response.data.data.settings[key].key] = response.data.data.settings[key].value;
		});
		commit('setting', settings);
		resolve();
	}).catch(() => {
		reject();
	});
}));