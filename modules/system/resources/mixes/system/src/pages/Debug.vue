<script>
	import injection, {trans} from '../helpers/injection';

	export default {
		beforeRouteEnter(to, from, next) {
			injection.loading.start();
			injection.store.dispatch('setting').then(() => {
				next(() => {
					injection.loading.finish();
				});
			}).catch(() => {
				injection.loading.error();
			});
		},
		computed : {
			enabled() {
				return this.$store.state.setting['debug.enabled'] === '1';
			},
			testing() {
				return this.$store.state.setting['debug.testing'] === '1';
			},
		},
		data() {
			return {
				loading : false,
			};
		},
		methods  : {
			clearCache() {
				const self = this;
				self.loading = true;
				self.$http.post(`${window.api}/system/cache/clear`).then(() => {
					self.$notice.open({
						title : '缓存清除成功！',
					});
				}).catch(() => {
					self.$notice.error({
						title : '缓存清除失败！',
					});
				}).finally(() => {
					self.loading = false;
				});
			},
			enableDebug(status) {
				const self = this;
				self.$loading.start();
				self.$http.post(`${window.api}/g/backend`, {
					query : `mutation {settings(key:"debug.enabled",value:"${status === true ? '1' : '0'}"){key,value}}`,
				}).then(() => {
					self.$loading.finish();
					self.$notice.success({
						title : '模式切换成功！',
					});
					self.$store.dispatch('setting');
				}).catch(() => {
					self.$loading.error();
					self.$notice.error({
						title : '模式切换失败！',
					});
				});
			},
			enableTesting(status) {
				const self = this;
				self.$loading.start();
				self.$http.post(`${window.api}/g/backend`, {
					query : `mutation {settings(key:"debug.testing",value:"${status === true ? '1' : '0'}"){key,value}}`,
				}).then(() => {
					self.$loading.finish();
					self.$notice.success({
						title : '模式切换成功！',
					});
					self.$store.dispatch('setting');
				}).catch(() => {
					self.$loading.error();
					self.$notice.error({
						title : '模式切换失败！',
					});
				});
			},
		},
		mounted() {
			this.$store.commit('title', trans('administration.title.debug'));
		},
	};
</script>
<template>
    <card :bordered="false">
        <p slot="title">调试工具</p>
        <i-form :label-width="200">
            <row>
                <i-col span="12">
                    <form-item label="缓存清除">
                        <i-button icon="ios-trash" :loading="loading" size="small" type="primary"
                                  @click.native="clearCache">
                            <span v-if="loading">正在清除...</span>
                            <span v-else>清除</span>
                        </i-button>
                    </form-item>
                </i-col>
            </row>
            <row>
                <i-col span="12">
                    <form-item label="Debug 模式">
                        <i-switch :value="enabled" size="large" @on-change="enableDebug">
                            <span slot="open">开启</span>
                            <span slot="close">关闭</span>
                        </i-switch>
                    </form-item>
                </i-col>
            </row>
            <row>
                <i-col span="12">
                    <form-item label="测试模式">
                        <i-switch :value="testing" size="large" @on-change="enableTesting">
                            <span slot="open">开启</span>
                            <span slot="close">关闭</span>
                        </i-switch>
                    </form-item>
                </i-col>
            </row>
        </i-form>
    </card>
</template>