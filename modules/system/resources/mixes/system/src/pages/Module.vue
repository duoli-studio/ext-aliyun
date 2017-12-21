<script>
	import file from '../helpers/export';
	import injection, {trans} from '../helpers/injection';

	const domainFields = [
		'alias',
		'default',
		'enabled',
		'host',
		'identification',
		'name'
	];

	const fields = [
		'authors',
		'description',
		'enabled',
		'identification',
		'name',
		'version'
	];

	export default {
		beforeRouteEnter(to, from, next) {
			injection.loading.start();
			injection.http.post(`${window.api}/administration`, {
				query : `query {
                    domains:moduleDomains{${domainFields.join(',')}},
                    enabled:modules(enabled:true){${fields.join(',')}},
                    installed:modules(installed:true){${fields.join(',')}},
                    modules{${fields.join(',')}},
                    notInstalled:modules(installed:false){${fields.join(',')}},
                }`,
			}).then(response => {
				next(vm => {
					const {
						      domains,
						      enabled,
						      installed,
						      modules,
						      notInstalled
					      } = response.data.data;
					vm.list.domains = domains;
					vm.list.enabled = enabled;
					vm.list.enabled = enabled;
					vm.list.installed = Object.keys(installed).map(key => {
						const data = installed[key];
						data.loading = false;

						return data;
					});
					vm.list.modules = modules;
					vm.list.notInstalled = Object.keys(notInstalled).map(key => {
						const data = notInstalled[key];
						data.loading = false;

						return data;
					});
					injection.loading.finish();
				});
			}).catch(() => {
				injection.loading.error();
			});
		},
		data() {
			const self = this;
			return {
				action      : `${window.api}/module/imports`,
				changed     : false,
				columns     : {
					exports      : [
						{
							align : 'center',
							type  : 'selection',
							width : 60,
						},
						{
							alias : 'right',
							key   : 'name',
							title : '模块名称',
							width : 200,
						},
						{
							key   : 'description',
							title : '描述',
							width : 400,
						},
						{
							key   : 'version',
							title : '版本',
						},
					],
					installed    : [
						{
							key   : 'name',
							title : '模块名称',
							width : 200,
						},
						{
							key   : 'authors',
							title : '作者',
							width : 200,
						},
						{
							key   : 'description',
							title : '描述',
						},
						{
							key   : 'status',
							render(h, data) {
								return h('i-switch', {
									on          : {
										input(value) {
											self.$loading.start();
											self.$http.put(`${window.api}/administration/modules/${data.row.identification.replace('/', '-')}`, {
												identification : data.row.identification,
												status         : value,
											}).then(() => {
												self.$loading.finish();
												self.$notice.open({
													title : value ? `开启模块${data.row.name}成功！` : `关闭模块${data.row.name}成功！`,
												});
												self.refresh();
												self.$store.dispatch('information');
											});
										},
									},
									props       : {
										size  : 'large',
										value : self.list.installed[data.index].enabled,
									},
									scopedSlots : {
										close() {
											return '关闭';
										},
										open() {
											return '开启';
										},
									},
								});
							},
							title : '状态',
							width : 200,
						},
						{
							key   : 'handle',
							render(h, data) {
								return h('i-button', {
									on    : {
										click() {
											self.uninstall(data.index);
										},
									},
									props : {
										loading : self.list.installed[data.index].loading,
										size    : 'small',
										type    : 'error',
									},
								}, '卸载');
							},
							title : '操作',
							width : 200,
						},
					],
					notInstalled : [
						{
							key   : 'name',
							title : '模块名称',
							width : 200,
						},
						{
							key   : 'authors',
							title : '作者',
							width : 200,
						},
						{
							key   : 'description',
							title : '描述',
						},
						{
							key   : 'handle',
							render(h, data) {
								return h('i-button', {
									on    : {
										click() {
											self.install(data.index);
										},
									},
									props : {
										loading : self.list.notInstalled[data.index].loading,
										type    : 'primary',
									},
								}, '安装');
							},
							title : '操作',
							width : 200
						},
					],
				},
				list        : {
					domains      : [],
					enabled      : [],
					exports      : [],
					installed    : [],
					modules      : [],
					notInstalled : []
				},
				loading     : {
					domains : false,
					exports : false,
					imports : false
				},
				multidomain : false,
				selection   : [],
			};
		},
		methods : {
			imports() {
				const self = this;
				self.$notice.open({
					title : '开始导入...'
				});
			},
			domainDefaultChanged(index) {
				const self = this;
				if (self.list.domains[index].default === true) {
					Object.keys(self.list.domains).forEach(i => {
						if (index !== parseInt(i, 0)) {
							self.$nextTick(() => {
								self.list.domains[i].default = false;
							});
						}
					});
				}
				else {
					Object.keys(self.list.domains).forEach(i => {
						if (self.list.domains[i].identification === 'notadd/notadd') {
							self.$nextTick(() => {
								self.list.domains[i].default = true;
							});
						}
					});
				}
			},
			exports() {
				const self = this;
				if (self.selection.length === 0) {
					self.$notice.error({
						title : '请先选择一个模块，再执行导出的操作！'
					});
				}
				else {
					self.$notice.open({
						title : '开始导出...',
					});
					self.loading.exports = true;
					const data = self.selection.map(module => module.identification);
					self.$http.post(`${window.api}/module/exports`, {
						modules : data,
					}).then(response => {
						file.download(response.data.data.file, response.data.data.content, 'yaml');
					}).catch(() => {
						self.$notice.error({
							title : '导出失败！',
						});
					}).finally(() => {
						self.loading.exports = false;
					});
				}
			},
			install(index) {
				const self = this;
				const item = self.list.notInstalled[index];
				item.loading = true;
				injection.http.post(`${window.api}/administration/modules`, {
					identification : item.identification,
				}).then(() => {
					self.$notice.open({
						title : '安装模块成功！',
					});
					self.refresh();
				}).finally(() => {
					item.loading = false;
				});
			},
			refresh() {
				const self = this;
				self.$notice.open({
					title : '正在刷新数据……',
				});
				self.$loading.start();
				injection.http.post(`${window.api}/administration`, {
					query : `query {
                        domains:moduleDomains{${domainFields.join(',')}},
                        enabled:modules(enabled:true){${fields.join(',')}},
                        installed:modules(installed:true){${fields.join(',')}},
                        modules{${fields.join(',')}},
                        notInstalled:modules(installed:false){${fields.join(',')}},
                    }`,
				}).then(result => {
					self.$loading.finish();
					const {
						      enabled,
						      installed,
						      modules,
						      notInstalled,
					      } = result.data.data;
					self.$nextTick(() => {
						self.list.enabled = enabled;
						self.list.installed = Object.keys(installed).map(key => {
							const data = installed[key];
							data.loading = false;

							return data;
						});
						self.list.modules = modules;
						self.list.notInstalled = Object.keys(notInstalled).map(key => {
							const data = notInstalled[key];
							data.loading = false;

							return data;
						});
						self.$notice.open({
							title : '刷新数据完成！',
						});
					});
				});
			},
			selectionChanged(selection) {
				this.selection = selection;
			},
			uninstall(index) {
				const self = this;
				const module = self.list.installed[index];
				module.loading = true;
				if (module.enabled) {
					self.$notice.error({
						title : `必须先关闭模块${module.name}，才能卸载！`,
					});
					module.loading = false;
				}
				else {
					const identification = module.identification.replace('/', '-');
					self.$http.delete(`${window.api}/administration/modules/${identification}`).then(() => {
						self.$notice.open({
							title : '卸载模块成功！',
						});
						self.refresh();
					}).catch(() => {
						self.$notice.error({
							title : '卸载模块失败！',
						});
					}).finally(() => {
						module.loading = false;
					});
				}
			},
			updateDomain() {
				const self = this;
				self.$loading.start();
				self.$http.post(`${window.api}/administration/modules/domains`, {
					data : self.list.domains,
				}).then(() => {
					self.$loading.finish();
					self.$notice.open({
						title : '更新模块域名信息成功！',
					});
					self.refresh();
				}).catch(() => {
					self.$loading.error();
					self.$notice.error({
						title : '更新模块域名信息失败！',
					});
				});
			},
			uploadBefore() {
				this.$loading.start();
			},
			uploadError() {
				this.$loading.error();
			},
			uploadFormatError(upload) {
				this.$loading.error();
				this.$notice.warning({
					title : '文件格式不正确',
					desc  : `文件 ${upload.name} 格式不正确`,
				});
			},
			uploadSuccess(data) {
				const self = this;
				self.$loading.finish();
				self.$notice.open({
					title : data.message,
				});
			},
		},
		mounted() {
			this.$store.commit('title', trans('administration.title.module'));
		},
		watch   : {
			'list.domains' : {
				deep : true,
				handler(val, old) {
					if (old.length > 0) {
						this.changed = true;
					}
				},
			},
		},
	};
</script>
<template>
    <div class="module-wrap">
        <tabs value="installed">
            <tab-pane label="开启模块" name="installed">
                <card :bordered="false">
                    <i-table :columns="columns.installed" :data="list.installed"></i-table>
                </card>
            </tab-pane>
            <tab-pane label="域名配置" name="domain">
                <card :bordered="false">
                    <div style="margin-bottom: 16px" v-if="changed">
                        <p style="color: #aa0000; display: inline-block; height: 32px; line-height: 32px; margin-right: 10px;">
                            数据已修改！修改后请批量更新数据！</p>
                        <div style="float: right;">
                            <i-button :loading="loading.domains" type="primary" @click.native="updateDomain">
                                <span v-if="loading.domains">批量更新中...</span>
                                <span v-else>批量更新</span>
                            </i-button>
                        </div>
                    </div>
                    <i-form ref="form">
                        <div class="ivu-table-wrapper">
                            <div class="ivu-table">
                                <div class="ivu-table-header">
                                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <colgroup>
                                            <col width="200">
                                            <col width="300">
                                            <col width="60">
                                            <col width="300">
                                            <col>
                                            <col width="160">
                                        </colgroup>
                                        <thead>
                                        <tr>
                                            <th>
                                                <div class="ivu-table-cell"><span>模块名称</span></div>
                                            </th>
                                            <th>
                                                <div class="ivu-table-cell"><span>域名</span></div>
                                            </th>
                                            <th>
                                                <div class="ivu-table-cell"><span>默认</span></div>
                                            </th>
                                            <th>
                                                <div class="ivu-table-cell"><span>别名</span></div>
                                            </th>
                                            <th></th>
                                            <th>
                                                <div class="ivu-table-cell"><span>使用域名</span></div>
                                            </th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="ivu-table-body" v-if="list.domains.length">
                                    <table cellpadding="0" border="0" width="100%">
                                        <colgroup>
                                            <col width="200">
                                            <col width="300">
                                            <col width="60">
                                            <col width="300">
                                            <col>
                                            <col width="160">
                                        </colgroup>
                                        <tbody class="ivu-table-tbody">
                                        <tr class="ivu-table-row" v-for="(domain, index) in list.domains">
                                            <td>
                                                <div class="ivu-table-cell">{{ domain.name }}</div>
                                            </td>
                                            <td>
                                                <div class="ivu-table-cell">
                                                    <template v-if="multidomain">
                                                        <i-input placeholder="请填写不带 http:// 或 https:// 的域名"
                                                                 v-model="list.domains[index].host"></i-input>
                                                    </template>
                                                    <template v-else>
                                                        <tooltip content="多域名功能未开启" placement="right-end">
                                                            <i-input :disabled="true"
                                                                     placeholder="请填写不带 http:// 或 https:// 的域名"
                                                                     v-model="list.domains[index].host"></i-input>
                                                        </tooltip>
                                                    </template>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="ivu-table-cell">
                                                    <template v-if="domain.identification !== 'notadd/api'">
                                                        <checkbox v-model="list.domains[index].default"
                                                                  @on-change="domainDefaultChanged(index)"></checkbox>
                                                    </template>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="ivu-table-cell">
                                                    <template v-if="domain.identification === 'notadd/notadd'">/
                                                    </template>
                                                    <template v-else>
                                                        <i-input v-model="list.domains[index].alias"></i-input>
                                                    </template>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="ivu-table-cell"></div>
                                            </td>
                                            <td>
                                                <div class="ivu-table-cell">
                                                    <i-switch :disabled="!multidomain" size="large"
                                                              v-model="list.domains[index].enabled">
                                                        <span slot="close">关闭</span>
                                                        <span slot="open">开启</span>
                                                    </i-switch>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </i-form>
                </card>
            </tab-pane>
            <tab-pane label="导入/导出" name="exchange">
                <card :bordered="false">
                    <div style="margin-bottom: 20px">
                        <upload :action="action"
                                :before-upload="uploadBefore"
                                :format="['yaml']"
                                :headers="{
                                    Authorization: `Bearer ${$store.state.token.access_token}`
                                }"
                                :on-error="uploadError"
                                :on-format-error="uploadFormatError"
                                :on-success="uploadSuccess"
                                :show-upload-list="false"
                                style="float: left;margin-right: 10px;">
                            <i-button :loading="loading.imports"
                                      type="ghost"
                                      icon="ios-cloud-upload-outline">
                                <span v-if="!loading.imports">导入</span>
                                <span v-else>正在导入…</span>
                            </i-button>
                        </upload>
                        <i-button :loading="loading.exports" type="default" @click.native="exports">
                            <span v-if="!loading.exports">导出</span>
                            <span v-else>正在导出…</span>
                        </i-button>
                    </div>
                    <i-table :columns="columns.exports" :data="list.exports"
                             @on-selection-change="selectionChanged"></i-table>
                </card>
            </tab-pane>
            <tab-pane label="本地安装" name="no-installed">
                <card :bordered="false">
                    <i-table :columns="columns.notInstalled" :data="list.notInstalled"></i-table>
                </card>
            </tab-pane>
        </tabs>
    </div>
</template>