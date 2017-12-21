import Vue from 'vue';
import Router from 'vue-router';

import Addon from '../pages/Addon.vue';
import Debug from '../pages/Debug.vue';
import Dashboard from '../pages/Dashboard.vue';
import Extension from '../pages/Extension.vue';
import Layout from '../layouts/Layout.vue';
import Login from '../pages/Login.vue';
import Mail from '../pages/Mail.vue';
import Menu from '../pages/Menu.vue';
import Module from '../pages/Module.vue';
import Navigation from '../pages/Navigation.vue';
import Seo from '../pages/Seo.vue';
import SeoRule from '../pages/SeoRule.vue';
import SeoRuleTemplate from '../pages/SeoRuleTemplate.vue';
import Template from '../pages/Template.vue';
import Upload from '../pages/Upload.vue';

import requireAuth from '../middlewares/auth';
import store from '../stores';

Vue.use(Router);

const configuration = [
	{
		beforeEnter : requireAuth,
		component   : Addon,
		path        : 'addon',
	},
	{
		beforeEnter : requireAuth,
		component   : Debug,
		path        : 'debug',
	},
	{
		beforeEnter : requireAuth,
		component   : Extension,
		path        : 'extension',
	},
	{
		beforeEnter : requireAuth,
		component   : Mail,
		path        : 'mail',
	},
	{
		beforeEnter : requireAuth,
		component   : Menu,
		path        : 'menu',
	},
	{
		beforeEnter : requireAuth,
		component   : Module,
		path        : 'module',
	},
	{
		beforeEnter : requireAuth,
		component   : Navigation,
		path        : 'navigation',
	},
	{
		beforeEnter : requireAuth,
		component   : Seo,
		path        : 'seo',
	},
	{
		beforeEnter : requireAuth,
		component   : SeoRule,
		path        : 'seo/:module',
	},
	{
		beforeEnter : requireAuth,
		component   : SeoRuleTemplate,
		path        : 'seo/:module/:rule',
	},
	{
		beforeEnter : requireAuth,
		component   : Template,
		path        : 'template',
	},
	{
		beforeEnter : requireAuth,
		component   : Upload,
		path        : 'upload',
	},
];

export default {
	init(injection) {
		injection.layout = Layout;
		const routes = [
			{
				children  : [
					{
						beforeEnter : requireAuth,
						component   : Dashboard,
						path        : '/',
					},
					...configuration,
					...injection.routes.global,
					...injection.routes.extension,
					...injection.routes.module,
					...injection.routes.other,
				],
				component : injection.layout,
				path      : '/',
			},
			{
				component : Login,
				path      : '/login',
			},
		];
		const router = new Router({
			routes,
		});
		router.beforeEach((to, from, next) => {
			if (store.state.loading === true) {
				let refresh;
				if (to.matched.length === 0) {
					store.commit('refresh', to.path);
					refresh = '/';
				}
				else {
					refresh = to.path;
				}
				setTimeout(() => {
					next({
						path : refresh,
					});
				}, 10);
			}
			else if (to.matched.length !== 0) {
				next();
			}
			else {
				injection.notice.error({
					title : '所访问的页面不存在，即将跳转...',
				});
				next({
					path : '/',
				});
			}
		});
		return router;
	},
};