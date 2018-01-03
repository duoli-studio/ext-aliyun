import {
	mixinAxios,
	mixinComponent,
	mixinLocal,
	mixinRouter,
	mixinUse,
} from '../mixes/injection';
import store from '../stores';
import {t} from '../local';

/**
 * 注入对象
 * @type {{addons: Array, modules: Array, routers: Array}}
 */
const injection = {
	addons  : [],
	modules : [],
	routers : [],
};

/**
 * 加载脚本 script
 * @param identification
 * @param url
 * @returns {Promise}
 */
function loadScript(identification, url) {
	return new Promise((resolve, reject) => {
		const script = document.createElement('script');
		script.type = 'text/javascript';
		if (script.readyState) {
			script.onreadystatechange = () => {
				if (script.readyState === 'loaded' || script.readyState === 'complete') {
					script.onreadystatechange = null;
					const instance = window[identification];
					if (instance && instance.default) {
						delete window[identification];
						resolve(instance.default);
					}
					else {
						reject(Error(`Do not support for [${url}]!`));
					}
					script.remove();
				}
			};
		}
		else {
			script.onload = () => {
				const instance = window[identification];
				if (instance && instance.default) {
					delete window[identification];
					resolve(instance.default);
				}
				else {
					reject(Error(`Do not support for [${url}]!`));
				}
				script.remove();
			};
		}
		script.onerror = () => {
			reject(Error(`${url} load error!`));
		};
		script.src = url;
		document.body.appendChild(script);
	});
}

/**
 * load style
 * @param url
 * @returns {Promise}
 */
function loadStylesheet(url) {
	return new Promise(() => {
		const stylesheet = document.createElement('link');
		stylesheet.rel = 'stylesheet';
		stylesheet.href = url;
		document.body.appendChild(stylesheet);
	});
}

/**
 * install
 * @param Vue
 */
function install(Vue) {
	injection.loadScript = loadScript;
	injection.loadStylesheet = loadStylesheet;
	injection.Vue = Vue;
	injection.store = store;
	mixinLocal(injection);
	mixinComponent(Vue, injection);
	mixinAxios(injection, Vue);
	const token = window.localStorage.getItem('token');
	if (token && token.length) {
		Vue.http.defaults.headers.common.Accept = 'application/json';
		Vue.http.defaults.headers.common.Authorization = `Bearer ${token}`;
	}
	mixinRouter(injection);
	mixinUse(injection);
	store.dispatch('information');
}

export const trans = t;

export default Object.assign(injection, {
	install,
});