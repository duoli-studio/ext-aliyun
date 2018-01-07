<script>
	import injection from '../helpers/injection';

	export default {
		beforeRouteEnter(to, from, next) {
			injection.loading.start();
			injection.http.post(`${window.api}/administration/seo`, {
				id     : to.params.rule,
				module : to.params.module,
			}).then(response => {
				next(vm => {
					vm.form = response.data.data;
					vm.module = response.data.module;
					vm.templates = response.data.templates;
					injection.loading.finish();
				});
			}).catch(() => {
				injection.loading.error();
			});
		},
		data() {
			return {
				changed   : false,
				form      : {
					id       : 0,
					module   : '',
					template : '',
				},
				loading   : false,
				module    : '',
				rules     : {
					template : [
						{
							required : true,
							type     : 'string',
							message  : '请输入模板代码',
							trigger  : 'change',
						},
					],
				},
				templates : [],
			};
		},
		methods : {
			selectedChange(value) {
				this.form.template = value;
			},
			update() {
				const self = this;
				self.$refs.form.validate(valid => {
					if (valid) {
						self.loading = true;
						self.$http.post(`${window.api}/administration/seo/edit`, self.form).then(() => {
							self.$notice.open({
								title : '更新数据成功！',
							});
							self.$router.push(`/seo/${self.$route.params.module}`);
						}).catch(() => {
							self.$notice.error({
								title : '更新数据失败！',
							});
						}).finally(() => {
							self.loading = false;
						});
					}
					else {
						self.$notice.error({
							title : '请填写模板代码！',
						});
					}
				});
			},
		},
		watch   : {
			form : {
				deep : true,
				handler() {
					const self = this;
					if (self.form.template.length) {
						this.changed = true;
					}
				},
			},
		},
	};
</script>
<style scoped>
    .monaco-editor {
        height : 500px;
        width  : 100%;
    }
</style>
<template>
    <div class="seo-wrap">
        <tabs :animated="false" style="overflow: visible;" value="current">
            <router-link slot="extra" style="display: block;height: 37px;line-height: 37px;"
                         :to="`/seo/${$route.params.module}`">返回
            </router-link>
            <tab-pane :label="`编辑模块：${module}的规则`" name="current">
                <card :bordered="false">
                    <div style="margin-bottom: 16px">
                        <i-select style="width: 200px;" @on-change="selectedChange">
                            <i-option :key="index" :value="template.template" v-for="(template, index) in templates">{{
                                template.name }}
                            </i-option>
                        </i-select>
                        <div style="float: right;" v-if="changed">
                            <p style="color: #aa0000; display: inline-block; margin-right: 10px;">数据已修改！修改后请更新数据！</p>
                            <i-button :loading="loading" type="primary" @click.native="update">
                                <span v-if="loading">更新中...</span>
                                <span v-else>更新</span>
                            </i-button>
                        </div>
                    </div>
                    <i-form :model="form" ref="form" :rules="rules">
                        <form-item label="" prop="template">
                            <monaco class="monaco-editor" language="php" v-model="form.template"></monaco>
                        </form-item>
                    </i-form>
                </card>
            </tab-pane>
        </tabs>
    </div>
</template>