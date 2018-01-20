<script>
	import injection from '../helpers/injection';
	import MenuChildren from './MenuChildren.vue';

	export default {
		beforeRouteEnter(to, from, next) {
			injection.loading.start();
			injection.http.get(`${window.api}/administration/menus`).then(response => {
				next(vm => {
					vm.items = Object.keys(response.data.data).map(index => response.data.data[index]);
					Object.keys(response.data.originals).forEach(index => {
						response.data.originals[index].expand = false;
					});
					vm.originals = response.data.originals;
					injection.loading.finish();
				});
			}).catch(() => {
				injection.loading.error();
			});
		},
		components : {
			MenuChildren,
		},
		data() {
			return {
				changed   : false,
				colspan   : 5,
				items     : [],
				loading   : false,
				originals : {},
				rules     : {
					order : [
						{
							message  : '排序不能为空',
							required : true,
							trigger  : 'change',
							type     : 'number',
						},
					],
					text  : [
						{
							required : true,
							message  : '菜单名称不能为空',
							trigger  : 'change',
							type     : 'string',
						},
					],
				},
			};
		},
		methods    : {
			expand(index) {
				this.originals[index].expand = !this.originals[index].expand;
			},
			refresh() {
				const self = this;
				self.$notice.open({
					title : '正在刷新数据...',
				});
				self.$loading.start();
				self.$http.get(`${window.api}/administration/menus`).then(response => {
					self.items = Object.keys(response.data.data)
						.map(index => response.data.data[index]);
					Object.keys(response.data.originals).forEach(index => {
						response.data.originals[index].expand = false;
					});
					self.originals = response.data.originals;
					self.$loading.finish();
					self.$notice.open({
						title : '刷新数据成功！',
					});
					self.$store.dispatch('information');
				}).catch(() => {
					self.$loading.error();
					self.$notice.error({
						title : '刷新数据失败！',
					});
				});
			},
			update() {
				const self = this;
				self.loading = true;
				self.$refs.form.validate(valid => {
					if (valid) {
						self.$http.post(`${window.api}/administration/menus`, {
							data : self.originals,
						}).then(() => {
							self.$notice.open({
								title : '数据批量更新成功！',
							});
							self.refresh();
						}).catch(() => {
							self.$notice.error({
								title : '数据批量更新失败！',
							});
						}).finally(() => {
							self.loading = false;
						});
					}
					else {
						self.$notice.error({
							title : '请正确填写完整的数据',
						});
					}
				});
			},
		},
		watch      : {
			originals : {
				deep : true,
				handler(val, old) {
					if (Object.keys(old).length > 0) {
						this.changed = true;
					}
				},
			},
		},
	};
</script>
<template>
    <div class="page-wrap">
        <tabs>
            <tab-pane label="菜单管理">
                <card :bordered="false">
                    <div style="margin-bottom: 16px" v-if="changed">
                        <p style="color: #aa0000; display: inline-block; height: 32px; line-height: 32px; margin-right: 10px;">
                            数据已修改！修改后请批量更新数据！</p>
                        <div style="float: right;">
                            <i-button :loading="loading" type="primary" @click.native="update">
                                <span v-if="loading">批量更新中...</span>
                                <span v-else>批量更新</span>
                            </i-button>
                        </div>
                    </div>
                    <i-form :model="originals" ref="form">
                        <div class="ivu-table-wrapper">
                            <div class="ivu-table">
                                <div class="ivu-table-header">
                                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <colgroup>
                                            <col width="60">
                                            <col width="200">
                                            <col width="400">
                                            <col>
                                            <col width="200">
                                        </colgroup>
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th>
                                                <div class="ivu-table-cell"><span>排序</span></div>
                                            </th>
                                            <th>
                                                <div class="ivu-table-cell"><span>菜单名称</span></div>
                                            </th>
                                            <th></th>
                                            <th>
                                                <div class="ivu-table-cell"><span>是否开启</span></div>
                                            </th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="ivu-table-body" v-if="items.length">
                                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <colgroup>
                                            <col width="60">
                                            <col width="200">
                                            <col width="400">
                                            <col>
                                            <col width="200">
                                        </colgroup>
                                        <tbody class="ivu-table-tbody">
                                        <template v-for="(item, index) in items">
                                            <tr class="ivu-table-row">
                                                <td>
                                                    <div class="ivu-table-cell ivu-table-cell-with-expand"
                                                         v-if="Object.keys(item.children).length > 0"
                                                         @click="expand(item.index)">
                                                        <template v-if="originals[item.index].expand">
                                                            <div class="ivu-table-cell-expand ivu-table-cell-expand-expanded">
                                                                <div class="ivu-table-cell-expand">
                                                                    <i class="ivu-icon ivu-icon-ios-arrow-right"></i>
                                                                </div>
                                                            </div>
                                                        </template>
                                                        <template v-else>
                                                            <div class="ivu-table-cell-expand">
                                                                <div class="ivu-table-cell-expand">
                                                                    <i class="ivu-icon ivu-icon-ios-arrow-right"></i>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="ivu-table-cell">
                                                        <form-item label=""
                                                                   :prop="item.index + '.order'"
                                                                   :rules="rules.order"
                                                                   style="margin-bottom: 0;">
                                                            <i-input :number="true"
                                                                     placeholder="请输入规则排序"
                                                                     v-model="originals[item.index].order"></i-input>
                                                        </form-item>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="ivu-table-cell">
                                                        <form-item label=""
                                                                   :prop="item.index + '.text'"
                                                                   :rules="rules.text"
                                                                   style="margin-bottom: 0;">
                                                            <i-input placeholder="请输入菜单名称"
                                                                     v-model="originals[item.index].text"></i-input>
                                                        </form-item>
                                                    </div>
                                                </td>
                                                <td></td>
                                                <td>
                                                    <div class="ivu-table-cell">
                                                        <form-item label=""
                                                                   :prop="item.index + '.text'"
                                                                   :rules="rules.text"
                                                                   style="margin-bottom: 0;">
                                                            <i-switch
                                                                    v-model="originals[item.index].enabled"></i-switch>
                                                        </form-item>
                                                    </div>
                                                </td>
                                            </tr>
                                            <menu-children :colspan="colspan"
                                                           :items="item.children"
                                                           :originals="originals"
                                                           :rules="rules"
                                                           v-if="Object.keys(item.children).length > 0 && originals[item.index].expand"/>
                                        </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="ivu-table-tip" v-if="items.length === 0">
                                    <table cellspacing="0" cellpadding="0" border="0">
                                        <tbody>
                                        <tr>
                                            <td><span>暂无筛选结果</span></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </i-form>
                </card>
            </tab-pane>
        </tabs>
    </div>
</template>