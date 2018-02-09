<script>
import injection, {trans} from '../helpers/injection';
import options from '../helpers/options';

export default {
	/**
	 *
	 * @param to     即将要进入的目标 路由对象
	 * @param from   当前导航正要离开的路由
	 * @param next   一定要调用该方法来 resolve 这个钩子。执行效果依赖 next 方法的调用参数
	 */
	beforeRouteEnter(to, from, next) {
		injection.loading.start();
		injection.http.post(`${window.api}backend/system/role/lists`, {
			type : 'backend',
			page : 1
		}).then(response => {
			const {status, message, data} = response.data;
			if (status) {
				throw new Error(message);
			}
			next(vm => {
				vm.list = data.list;
				vm.pagination = data.pagination;
				injection.loading.finish();
			});
		}).catch(error => {
			injection.notice.error({
				title : error
			});
			injection.loading.error();
		});
	},
	data() {
		const that = this;

		// 操作渲染函数
		const handleRender = (h, params) => {
			const btnPermission = h('i-button', {
				props : {
					type : 'warning',
					size : 'small'
				},
				style : {
					marginRight : '5px'
				},
				on    : {
					click : () => {
						that.fetchPermission(params);
					}
				}
			}, '权限');
			const btnEdit = h('i-button', {
				props : {
					type : 'primary',
					size : 'small'
				},
				style : {
					marginRight : '5px'
				},
				on    : {
					click : () => {
						that.fetchRole(params);
					}
				}
			}, '编辑');
			const btnDelete = h('poptip', {
				props : {
					confirm   : true,
					width     : 200,
					title     : '确认删除此角色 ? ',
					placement : 'left'
				},
				on    : {
					'on-ok' : () => {
						that.removeItem(params);
					}
				}
			}, [
				h('i-button', {
					props : {
						type : 'error',
						size : 'small'
					},
				}, '删除')
			]);
			const btns = [];
			if (params.row.can_permission) {
				btns.push(btnPermission);
			}
			btns.push(btnEdit);
			if (params.row.can_delete) {
				btns.push(btnDelete);
			}
			// 返回操作按钮
			return h('div', btns);
		};

		return {
			// 角色列表
			list              : [],
			// 分页
			pagination        : {
				page  : 1,
				size  : 15,
				total : 0,
			},
			// 定义的各种选项
			options,
			// 角色列定义
			columns           : [
				{
					title : '角色ID',
					key   : 'id'
				},
				{
					title : '角色标识',
					key   : 'name'
				},
				{
					title : '角色标题',
					key   : 'title'
				},
				{
					title  : '操作',
					key    : 'handle',
					align  : 'right',
					render : handleRender,
				},
			],
			filter            : {
				kw    : '',
				field : '',
				type  : 'backend',
			},
			modalItem         : {
				display : false,
				title   : '创建角色',
				type    : 'create',
				loading : false
			},
			// 编辑权限
			modalPermission   : {
				display : false,
				roleId  : 0
			},
			// 权限设置弹框信息
			permissions       : [],
			permissionValue   : [],
			loading           : false,
			rules             : {
				name  : [
					{
						message  : '角色标识不能为空',
						required : true,
						trigger  : 'blur',
					},
				],
				title : [
					{
						message  : '角色名称不能为空',
						required : true,
						trigger  : 'blur',
					},
				],
				guard : [
					{
						message  : '角色类型不能为空',
						required : true,
						trigger  : 'blur',
					},
				]
			},
			permissionColumns : [
				{
					title : '分组',
					key   : 'title'
				},
				{
					title  : '权限',
					key    : 'permissions',
					render : (h, params) => {
						const {permissions} = params.row;
						const checkboxes = [];
						if (permissions.length) {
							permissions.forEach((item) => {
								// description : "设置全局配置项"
								// id : 9
								// value : 0
								checkboxes.push(h('checkbox', {
									props : {
										value : !!item.value,
										label : item.id,
									},
									on    : {
										'on-change' : (status) => {
											if (status) {
												// remove from permissionValue
												that.permissionValue.push(item.id);
											}
											else {
												const index = this.permissionValue.indexOf(item.id);
												if (index >= 0) {
													that.permissionValue.splice(index, 1);
												}
											}
										}
									},
								}, item.description));
							});
						}
						return h('div', checkboxes);
					}
				},
			],
			item              : {
				id          : 0,
				name        : '',
				title       : '',
				guard       : '',
				description : '',
			},
			// 新增角色类型
			dsGuard           : {
				backend : '后台',
				web     : '用户',
				develop : '开发者'
			},
			dsField           : {
				id   : 'ID',
				name : '角色标识',
			},
		};
	},
	methods : {
		// 清空再次点击新增的数据
		resetItem() {
			this.item.id = 0;
			this.item.name = '';
			this.item.title = '';
			this.item.guard = '';
			this.item.description = '';
			this.modalItem.type = 'create';
		},
		resetPermission() {
			this.permissions = [];
			this.permissionValue = [];
			this.modalPermission.display = false;
		},
		// 删除数据
		removeItem(params) {
			const self = this;
			self.loading = true;
			injection.http.post(`${window.api}backend/system/role/do`, {
				action : 'delete',
				id     : params.row.id
			}).then((response) => {
				const {status, message} = response.data;
				if (status) {
					throw new Error(message);
				}
				self.$notice.open({
					title : '删除数据成功！',
				});
				self.refresh();
			}).catch((error) => {
				self.$loading.error();
				self.$notice.error({
					title : error,
				});
			}).finally(() => {
				self.loading = false;
			});
		},
		// 点击新增的弹出框
		createItem() {
			// 重置弹框数据
			this.resetItem();
			this.modalItem.display = true;
			this.modalItem.title = '创建角色';
		},
		// 刷新页面
		refresh() {
			const self = this;
			self.$loading.start();
			injection.http.post(`${window.api}backend/system/role/lists`, {
				type  : self.filter.type,
				field : self.filter.field,
				kw    : self.filter.kw,
				page  : self.pagination.page,
				size  : self.pagination.size,
			}).then(response => {
				const {status, message, data} = response.data;
				if (status) {
					throw new Error(message);
				}
				self.list = data.list;
				self.pagination = data.pagination;
				self.$notice.open({
					title : '刷新数据成功！',
				});
				self.$loading.finish();
			}).catch(error => {
				self.$loading.error();
				self.$notice.error({
					title : error,
				});
			});
			this.loading = false;
		},
		// 增加角色
		submitItem() {
			const self = this;
			let variable = {};
			if (self.modalItem.type === 'create') {
				variable = {
					name        : self.item.name,
					title       : self.item.title,
					guard       : self.item.guard,
					description : self.item.description,
				};
			}
			else {
				variable = {
					name        : self.item.name,
					title       : self.item.title,
					guard       : self.item.guard,
					description : self.item.description,
					id          : self.item.id
				};
			}
			self.$refs.item.validate(valid => {
				if (valid) {
					self.loading = true;
					self.$http.post(
						`${window.api}backend/system/role/establish`,
						variable
					).then((response) => {
						const {status, message} = response.data;
						if (status) {
							throw new Error(message);
						}
						self.$notice.open({
							title : '保存成功！',
						});
						// 重置弹窗
						self.resetItem();
						// 刷新页面
						self.refresh();
					}).catch(() => {
						self.$notice.error({
							title : '保存失败！',
						});
					}).finally(() => {
						self.loading = false;
						self.modalItem.display = false;
					});
				}
			});
		},

		submitPermission() {
			const self = this;
			self.loading = true;
			self.$http.post(`${window.api}backend/system/role/permissions_store`, {
				permission : self.permissionValue,
				role_id    : self.modalPermission.roleId,
			}).then(() => {
				self.$notice.open({
					title : '保存成功！',
				});
				// 重置弹窗
				self.resetPermission();
			}).catch(() => {
				self.$notice.error({
					title : '保存失败！',
				});
			}).finally(() => {
				self.loading = false;
				self.modalItem.display = false;
			});
		},
		// 编辑角色
		fetchRole(params) {
			this.modalItem.display = true;
			this.modalItem.type = 'edit';
			this.item.id = params.row.id;
			this.item.name = params.row.name;
			this.item.title = params.row.title;
			this.item.guard = params.row.type;
			this.item.description = params.row.description;
		},
		// 权限获取展示
		fetchPermission(params) {
			const self = this;
			this.modalPermission.display = true;
			this.modalPermission.roleId = params.row.id;
			self.loading = true;
			self.$http.post(`${window.api}backend/system/role/permissions`, {
				role_id : params.row.id
			}).then((resp) => {
				self.permissionValue = [];
				self.permissions = resp.data.data;
				self.permissions.forEach((global) => {
					global.groups.forEach((group) => {
						group.permissions.forEach((permission) => {
							if (permission.value) {
								self.permissionValue.push(permission.id);
							}
						});
					});
				});
			}).finally(() => {
				self.loading = false;
			});
		},
		// 查找
		search() {
			this.pagination.page = 1;
			this.refresh();
		},
		pageChange(page) {
			const self = this;
			self.pagination.page = page;
			self.refresh();
		},
		pageSizeChange(pagesize) {
			const self = this;
			self.pagination.size = pagesize;
			self.refresh();
		}
	},
	mounted() {
		this.$store.commit('title', trans('system.seo.system.pam_role'));
	},
};
</script>
<template>
	<card :bordered="false">
		<p slot="title">角色管理</p>
		<i-button slot="extra" type="info" size="small" @click.native="createItem">
			<icon type="android-add"></icon>
			新增
		</i-button>
		<i-form inline>
			<form-item prop="field">
				<i-select placeholder="查询类型" v-model="filter.field">
					<i-option v-for="(value ,type) in dsField" :value="type" :key="type">{{ value }}</i-option>
				</i-select>
			</form-item>
			<form-item prop="user">
				<i-input type="text" placeholder="关键词" v-model="filter.kw"></i-input>
			</form-item>
			<form-item prop="type">
				<i-select placeholder="用户类型" v-model="filter.type">
					<i-option v-for="(value ,type) in dsGuard" :value="type" :key="type">{{ value }}</i-option>
				</i-select>
			</form-item>
			<i-button class="btn-action" type="primary" @click.native="search">
				<icon type="search"></icon>
				搜索
			</i-button>
		</i-form>

		<i-table :columns="columns" :data="list"></i-table>

		<page show-sizer show-elevator
			  :current="pagination.page"
			  :page-size-opts="options.pageSize"
			  :total="pagination.total"
			  :page-size="pagination.size" class-name="liex-pager"
			  @on-change="pageChange"
			  @on-page-size-change="pageSizeChange"
		></page>

		<!--权限弹出框-->
		<modal v-model="modalPermission.display"
			   title="权限设置"
			   class="liex-modal-delete">
			<tabs>
				<tab-pane v-for="diction in permissions" :key="diction.key" :label="diction.title">
					<i-table :columns="permissionColumns" :data="diction.groups" size="small"></i-table>
				</tab-pane>
			</tabs>
			<i-button style="margin-top:10px;" class="btn-action" type="success" @click.native="submitPermission">保存
			</i-button>
		</modal>
		<!--新增弹出框start-->
		<modal v-model="modalItem.display" class="liex-modal-delete"
			   :title="modalItem.title">
			<i-form ref="item" :model="item" :rules="rules" :label-width="110">
				<form-item label="角色名称" prop="title">
					<i-input v-model="item.title"></i-input>
				</form-item>
				<form-item label="角色标识(英文)" prop="name">
					<i-input v-model="item.name"
							 :disabled="modalItem.type !== 'create'"></i-input>
				</form-item>
				<form-item label="角色类型" prop="guard">
					<i-select v-model="item.guard" :disabled="modalItem.type !== 'create'">
						<i-option v-for="(label, key) in dsGuard" :value="key" :label="label"
								  :key="key"></i-option>
					</i-select>
				</form-item>
				<form-item label="角色描述" prop="description">
					<i-input v-model="item.description" type="textarea"></i-input>
				</form-item>
				<form-item>
					<i-button :loading="loading" @click.native="submitItem"
							  class="btn-group" type="success">
						<span v-if="!loading">{{modalItem.type === 'create' ? '创建' : '编辑'}}</span>
						<span v-else>正在提交…</span>
					</i-button>
				</form-item>
			</i-form>
		</modal>
	</card>
</template>