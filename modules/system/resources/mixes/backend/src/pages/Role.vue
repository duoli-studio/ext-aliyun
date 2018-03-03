<script>
import injection, {trans} from '../helpers/injection';

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
				vm.list.items = data.list;
				vm.list.pagination = data.pagination;
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
					type  : 'warning',
					size  : 'small',
					icon  : 'settings',
					shape : 'circle',
				},
				style : {
					marginRight : '5px'
				},
				on    : {
					click : () => {
						that.onPermission(params);
					}
				}
			});
			const btnEdit = h('i-button', {
				props : {
					type  : 'primary',
					size  : 'small',
					icon  : 'edit',
					shape : 'circle',
				},
				style : {
					marginRight : '5px'
				},
				on    : {
					click : () => {
						that.onC2eEdit(params);
					}
				}
			});
			const btnDelete = h('poptip', {
				props : {
					confirm   : true,
					width     : 200,
					title     : '确认删除此角色 ? ',
					placement : 'left'
				},
				on    : {
					'on-ok' : () => {
						that.onDo(params, 'delete');
					}
				}
			}, [
				h('i-button', {
					props : {
						type  : 'error',
						size  : 'small',
						icon  : 'close',
						shape : 'circle',
					},
					style : {
						marginRight : '5px'
					},
				})
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
			list       : {
				items      : [],
				pagination : {
					page  : 1,
					size  : 20,
					total : 0,
				},
				filter     : {
					kw    : '',
					field : '',
					type  : 'backend',
				},
				columns    : [
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
			},
			c2e        : {
				display : false,
				title   : '创建角色',
				type    : 'create',
				loading : false,
				data    : {
					id          : 0,
					name        : '',
					title       : '',
					guard       : '',
					description : '',
				},
				rules   : {
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
			},
			do         : {
				loading : false
			},
			// 编辑权限
			permission : {
				display : false,
				loading : false,
				role_id : 0,
				items   : [],
				data    : [],
				columns : [
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
													// remove from
													that.permission.data.push(item.id);
												}
												else {
													const index = this.permission.data.indexOf(item.id);
													if (index >= 0) {
														that.permission.data.splice(index, 1);
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
			},
			dataSource : {
				guard : {
					backend : '后台',
					user    : '用户',
					develop : '开发者'
				},
				field : {
					id   : 'ID',
					name : '角色标识',
				}
			},
		};
	},
	methods : {
		// 清空再次点击新增的数据
		c2eReset() {
			this.c2e.data.id = 0;
			this.c2e.data.name = '';
			this.c2e.data.title = '';
			this.c2e.data.guard = '';
			this.c2e.data.description = '';
			this.c2e.type = 'create';
		},
		resetPermission() {
			this.permission.items = [];
			this.permission.data = [];
			this.permission.display = false;
		},
		// 删除数据
		onDo(params, action) {
			const self = this;
			self.do.loading = true;
			injection.http.post(`${window.api}backend/system/role/do`, {
				action,
				id : params.row.id
			}).then((response) => {
				const {status, message} = response.data;
				if (status) {
					throw new Error(message);
				}
				self.$notice.open({
					title : '删除数据成功！',
				});
				self.listRefresh();
			}).catch((error) => {
				self.$notice.error({
					title : '操作失败',
					desc  : error,
				});
			}).finally(() => {
				self.do.loading = false;
			});
		},
		// add
		onC2eAdd() {
			// 重置弹框数据
			this.c2eReset();
			this.c2e.display = true;
			this.c2e.title = '创建角色';
		},
		// edit
		onC2eEdit(params) {
			this.c2e.display = true;
			this.c2e.type = 'edit';
			this.c2e.data.id = params.row.id;
			this.c2e.data.name = params.row.name;
			this.c2e.data.title = params.row.title;
			this.c2e.data.guard = params.row.type;
			this.c2e.data.description = params.row.description;
		},
		// 增加角色
		onC2eSubmit() {
			const self = this;
			let variable = {};
			if (self.c2e.type === 'create') {
				variable = {
					name        : self.c2e.data.name,
					title       : self.c2e.data.title,
					guard       : self.c2e.data.guard,
					description : self.c2e.data.description,
				};
			}
			else {
				variable = {
					name        : self.c2e.data.name,
					title       : self.c2e.data.title,
					guard       : self.c2e.data.guard,
					description : self.c2e.data.description,
					id          : self.c2e.data.id
				};
			}
			self.$refs.c2e.validate(valid => {
				if (valid) {
					self.c2e.loading = true;
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
						self.c2eReset();
						// 刷新页面
						self.listRefresh();
						self.c2e.display = false;
					}).catch((error) => {
						self.$notice.error({
							title : '保存失败！',
							desc  : error,
						});
						self.c2e.loading = false;
					});
				}
			});
		},

		onPermissionSubmit() {
			const self = this;
			self.permission.loading = true;
			self.$http.post(`${window.api}backend/system/role/permissions_store`, {
				permission : self.permission.data,
				role_id    : self.permission.role_id,
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
				self.permission.loading = false;
				self.permission.display = false;
			});
		},
		// 编辑角色

		// 权限获取展示
		onPermission(params) {
			const self = this;
			this.permission.display = true;
			this.permission.role_id = params.row.id;
			self.$http.post(`${window.api}backend/system/role/permissions`, {
				role_id : params.row.id
			}).then((resp) => {
				self.permission.data = [];
				self.permission.items = resp.data.data;
				self.permission.items.forEach((global) => {
					global.groups.forEach((group) => {
						group.permissions.forEach((permission) => {
							if (permission.value) {
								self.permission.data.push(permission.id);
							}
						});
					});
				});
			});
		},
		listRefresh() {
			const self = this;
			self.$loading.start();
			injection.http.post(`${window.api}backend/system/role/lists`, {
				type  : self.list.filter.type,
				field : self.list.filter.field,
				kw    : self.list.filter.kw,
				page  : self.list.pagination.page,
				size  : self.list.pagination.size,
			}).then(response => {
				const {status, message, data} = response.data;
				if (status) {
					throw new Error(message);
				}
				self.list.items = data.list;
				self.list.pagination = data.pagination;
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
		},
		listSearch() {
			this.list.pagination.page = 1;
			this.listRefresh();
		},
		listPageChange(page) {
			const self = this;
			self.list.pagination.page = page;
			self.listRefresh();
		},
		listPageSizeChange(pagesize) {
			const self = this;
			self.list.pagination.size = pagesize;
			self.listRefresh();
		},
	},
	mounted() {
		this.$store.commit('title', trans('system.seo.system.pam_role'));
	},
};
</script>
<template>
	<card :bordered="false">
		<p slot="title">角色管理</p>
		<i-button slot="extra" type="info" size="small" @click.native="onC2eAdd">
			<icon type="android-add"></icon>
			新增
		</i-button>
		<i-form inline>
			<form-item prop="field">
				<i-select placeholder="查询类型" v-model="list.filter.field" style="width: 100px">
					<i-option v-for="(value ,type) in dataSource.field" :value="type" :key="type">{{ value }}</i-option>
				</i-select>
			</form-item>
			<form-item prop="user">
				<i-input type="text" placeholder="关键词" v-model="list.filter.kw"></i-input>
			</form-item>
			<form-item prop="type">
				<i-select placeholder="用户类型" v-model="list.filter.type">
					<i-option v-for="(value ,type) in dataSource.guard" :value="type" :key="type">{{ value }}</i-option>
				</i-select>
			</form-item>
			<i-button class="btn-action" type="primary" @click.native="listSearch">
				<icon type="search"></icon>
				搜索
			</i-button>
		</i-form>
		<i-table :columns="list.columns" :data="list.items"></i-table>
		<!--pagination-->
		<page show-sizer show-elevator
			  :current="list.pagination.page"
			  :total="list.pagination.total"
			  :page-size="list.pagination.size" class-name="liex-pager"
			  @on-change="listPageChange"
			  @on-page-size-change="listPageSizeChange"
		></page>

		<!--权限弹出框-->
		<modal v-model="permission.display"
			   title="权限设置"
			   class="liex-modal-delete">
			<tabs>
				<tab-pane v-for="diction in permission.items" :key="diction.key" :label="diction.title">
					<i-table :columns="permission.columns" :data="diction.groups" size="small"></i-table>
				</tab-pane>
			</tabs>
			<i-button style="margin-top:10px;" class="btn-action" type="success" @click.native="onPermissionSubmit">保存
			</i-button>
		</modal>
		<!--新增弹出框start-->
		<modal v-model="c2e.display" class="liex-modal-delete"
			   :title="c2e.title">
			<i-form ref="c2e" :model="c2e.data" :rules="c2e.rules" :label-width="110">
				<form-item label="角色名称" prop="title">
					<i-input v-model="c2e.data.title"></i-input>
				</form-item>
				<form-item label="角色标识(英文)" prop="name">
					<i-input v-model="c2e.data.name"
							 :disabled="c2e.type !== 'create'"></i-input>
				</form-item>
				<form-item label="角色类型" prop="guard">
					<i-select v-model="c2e.data.guard" :disabled="c2e.type !== 'create'">
						<i-option v-for="(label, key) in dataSource.guard" :value="key" :label="label"
								  :key="key"></i-option>
					</i-select>
				</form-item>
				<form-item label="角色描述" prop="description">
					<i-input v-model="c2e.data.description" type="textarea"></i-input>
				</form-item>
				<form-item>
					<i-button :loading="c2e.loading" @click.native="onC2eSubmit"
							  class="btn-group" type="success">
						<span v-if="!c2e.loading">{{c2e.type === 'create' ? '创建' : '编辑'}}</span>
						<span v-else>正在提交…</span>
					</i-button>
				</form-item>
			</i-form>
		</modal>
	</card>
</template>