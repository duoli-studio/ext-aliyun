<script>
	import file from '../helpers/export';
	import injection from '../helpers/injection';

	const fields = [
		'description',
		'enabled',
		'identification',
		'name',
		'version',
	];

	export default {
		beforeRouteEnter(to, from, next) {
			injection.loading.start();
			injection.http.post(`${window.api}/administration`, {
				query : `query {
                    addons{${fields.join(',')}},
                    enabled:addons(enabled:true){${fields.join(',')}},
                    installed:addons(installed:true){${fields.join(',')}},
                    notInstall:addons(installed:false){${fields.join(',')}},
                }`,
			}).then(response => {
				next(vm => {
					const {
						      addons,
						      enabled,
						      installed,
						      notInstall,
					      } = response.data.data;
					vm.list.enabled = enabled;
					vm.list.addons = addons;
					vm.list.installed = installed;
					vm.list.notInstalled = Object.keys(notInstall).map(key => {
						const data = notInstall[key];
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
				action    : `${window.api}/addons/imports`,
				columns   : {
					exports      : [
						{
							align : 'center',
							type  : 'selection',
							width : 60,
						},
						{
							alias : 'right',
							key   : 'name',
							title : '插件名称',
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
							title : '插件名称',
							width : 200,
						},
						{
							key   : 'author',
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
											self.$http.put(`${window.api}/administration/addons/${data.row.identification.replace('/', '-')}`, {
												identification : data.row.identification,
												status         : value,
											}).then(() => {
												self.$loading.finish();
												self.$notice.open({
													title : value ? `开启插件${data.row.name}成功！` : `关闭插件${data.row.name}成功！`,
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
											return h('span', '关闭');
										},
										open() {
											return h('span', '开启');
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
											const addon = self.list.installed[data.index];
											addon.loading = true;
											if (addon.enabled) {
												self.$notice.error({
													title : `必须先关闭插件${addon.name}，才能卸载！`,
												});
												addon.loading = false;
											}
											else {
												const identification = addon.identification.replace('/', '-');
												self.$http.delete(`${window.api}/administration/addons/${identification}`).then(() => {
													self.$notice.open({
														title : '插件卸载成功！',
													});
													self.refresh();
												}).catch(() => {
													self.$notice.error({
														title : '插件卸载失败！',
													});
												}).finally(() => {
													addon.loading = false;
												});
											}
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
							title : '插件名称',
							width : 200,
						},
						{
							key   : 'author',
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
											const item = self.list.notInstalled[data.index];
											item.loading = true;
											self.$http.post(`${window.api}/administration/addons`, {
												identification : item.identification,
											}).then(() => {
												self.$notice.open({
													title : '安装插件成功！',
												});
												self.refresh();
											}).finally(() => {
												item.loading = false;
											});
										},
									},
									props : {
										loading : self.list.notInstalled[data.index].loading,
										size    : 'small',
										type    : 'primary',
									},
								}, '安装');
							},
							title : '操作',
							width : 200,
						},
					],
				},
				list      : {
					enabled      : [],
					addons       : [],
					installed    : [],
					notInstalled : [],
				},
				loading   : {
					exports : false,
					imports : false,
				},
				selection : [],
			};
		},
		methods : {
			exports() {
				const self = this;
				if (self.selection.length === 0) {
					self.$notice.error({
						title : '请先选择一个插件，再执行导出的操作！',
					});
				}
				else {
					self.$notice.open({
						title : '开始导出...',
					});
					self.loading.exports = true;
					const data = self.selection.map(module => module.identification);
					self.$http.post(`${window.api}/addons/exports`, {
						addons : data,
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
			refresh() {
				const self = this;
				self.$notice.open({
					title : '正在刷新数据……',
				});
				self.$loading.start();
				self.$http.post(`${window.api}/administration`, {
					query : `query {
                    addons{${fields.join(',')}},
                    enabled:addons(enabled:true){${fields.join(',')}},
                    installed:addons(installed:true){${fields.join(',')}},
                    notInstall:addons(installed:false){${fields.join(',')}},
                    }`,
				}).then(result => {
					self.$loading.finish();
					const {
						      addons,
						      enabled,
						      installed,
						      notInstall,
					      } = result.data.data;
					self.$nextTick(() => {
						self.list.enabled = Object.keys(enabled).map(key => enabled[key]);
						self.list.addons = Object.keys(addons).map(key => addons[key]);
						self.list.installed = Object.keys(installed).map(key => {
							const data = installed[key];
							data.loading = false;

							return data;
						});
						self.list.notInstalled = Object.keys(notInstall).map(key => {
							const data = notInstall[key];
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
			this.$store.commit('title', '插件管理');
		},
	};
</script>
<template>
    <div class="module-wrap">
        <tabs value="installed">
            <tab-pane label="开启插件" name="installed">
                <card :bordered="false">
                    <i-table :columns="columns.installed" :data="list.installed"></i-table>
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
                    <i-table :columns="columns.exports" :data="list.addons"
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