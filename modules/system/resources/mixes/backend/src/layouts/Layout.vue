<script>
import injection from '../helpers/injection';
import Page from '../pages/Page.vue';

export default {
	computed : {
		design : {
			get() {
				return this.$store.state.design;
			},
			set(value) {
				this.$store.commit('design', value);
			},
		},
		name() {
			return this.$store.state.username;
		},
		navigation() {
			return this.$store.state.navigation;
		},
		pages() {
			return this.$store.state.page;
		},
		scripts() {
			return this.$store.state.script;
		},
		sidebar() {
			return this.$store.state.sidebar;
		},
		stylesheets() {
			return this.$store.state.stylesheet;
		},
	},
	data() {
		return {
			hideSidebar : false,
			imports     : [],
			url         : window.url,
		};
	},
	methods  : {
		logout() {
			window.localStorage.clear();
			this.$router.push('/login');
		},
		loadImports() {
			const self = this;
			Promise.all(self.imports).then(data => {
				Object.keys(data).forEach(index => {
					const instance = data[index];
					if (instance.install) {
						instance.install(injection);
					}
				});
				self.$router.addRoutes([
					{
						children  : [
							...injection.routers,
						],
						component : injection.layout,
						path      : '/',
					},
				]);
				self.imports = [];
				if (self.$store.state.refresh.length) {
					self.$notice.open({
						title : '即将跳转...',
					});
					self.$router.push(self.$store.state.refresh);
					self.$store.commit('refresh', '');
				}
			});
		},
		loadScripts() {
			const self = this;
			Object.keys(self.scripts).forEach(index => {
				const script = self.scripts[index];
				self.imports.push(injection.loadScript(script.identification, script.file));
			});
		},
		loadStylesheets() {
			const self = this;
			Object.keys(self.stylesheets).forEach(index => {
				const stylesheet = self.stylesheets[index];
				injection.loadStylesheet(stylesheet.file);
			});
		},
		mappingPages(pages) {
			const self = this;
			if (typeof pages === 'object') {
				Object.keys(pages).forEach(index => {
					const definition = pages[index];
					switch (definition.initialization.target) {
						case 'extension':
							break;
						case 'global':
							self.$router.addRoutes([
								{
									children  : [
										{
											beforeEnter : injection.middleware.requireAuth,
											component   : Page,
											path        : definition.initialization.path,
										},
									],
									component : injection.layout,
									path      : '/',
								},
							]);
							break;
						default:
							break;
					}
				});
			}
		},
		saveDashboard() {
			this.$children.filter(item => item.$options.name === 'Administration-Dashboard').forEach(item => {
				if (item.saveDashboard) {
					item.saveDashboard();
				}
			});
		},
		switchMode() {
			this.design = !this.design;
		},
		switchSidebar(navigation) {
			if (navigation.children) {
				this.$store.commit('sidebar', navigation.children);
			}
		},
		toggleClick() {
			this.hideSidebar = !this.hideSidebar;
		},
	},
	mounted() {
		const self = this;
		self.$http.post(`${window.api}pam/auth/access`).then(response => {
			const username = response.data.data.username
				? response.data.data.username
				: response.data.data.id;
			self.$store.commit('username', username);
		});
		self.mappingPages(self.pages);
		self.loadScripts();
		self.loadStylesheets();
		self.loadImports();
	},
	watch    : {
		imports(val) {
			if (val.length) {
				this.loadImports();
			}
		},
		pages(pages) {
			this.mappingPages(pages);
		},
		scripts() {
			this.loadScripts();
		},
		stylesheets() {
			this.loadStylesheets();
		},
	},
};
</script>
<template>
	<div class="layout" :class="{'layout-hide-sidebar': hideSidebar}">
		<div class="layout-left">
			<i-menu :accordion="true" theme="dark" width="auto">
				<div class="layout-logo-left"></div>
				<template v-for="(item, key) in sidebar">
					<submenu :name="'sidebar-' + key" v-if="item.children">
						<template slot="title">
							<icon :type="item.icon"></icon>
							{{ item.text }}
						</template>
						<menu-item :name="'sidebar-' + key + '-' + index" v-for="(sub, index) in item.children"
								   :key="index">
							<router-link :to="sub.path">{{ sub.text }}</router-link>
						</menu-item>
					</submenu>
					<menu-item :name="'sidebar-' + key" v-else>
						<router-link :to="item.path">
							<icon :type="item.icon"></icon>
							{{ item.text }}
						</router-link>
					</menu-item>
				</template>
			</i-menu>
		</div>
		<div class="layout-header">
			<i-button type="text" @click.native="toggleClick">
				<icon size="32" type="navicon"></icon>
			</i-button>
			<i-menu mode="horizontal" theme="light">
				<menu-item :name="'nav-' + key" v-for="(nav, key) in navigation" :key="key"
						   @click.native="switchSidebar(nav)">
					<router-link :to="nav.path">
						<icon :type="nav.icon"></icon>
						{{ nav.text }}
					</router-link>
				</menu-item>
			</i-menu>
			<i-menu mode="horizontal" theme="light">
				<menu-item name="right-0" v-if="design">
					<i-button type="primary" @click.native="saveDashboard">保存布局</i-button>
				</menu-item>
				<menu-item name="right-1">
					<a :href="url" target="_blank">前台</a>
				</menu-item>
				<submenu name="right-2">
					<template slot="title">
						<icon type="person"></icon>
						{{ name }}
					</template>
					<menu-item name="right-2-0" @click.native="switchMode" v-if="$route.path === '/' && !design">编辑模式
					</menu-item>
					<menu-item name="right-2-1" @click.native="logout">退出</menu-item>
				</submenu>
			</i-menu>
		</div>
		<div class="layout-content">
			<div class="layout-content-main">
				<router-view></router-view>
			</div>
			<div class="layout-copy" v-if="!design">2017 &copy; Notadd</div>
		</div>
	</div>
</template>