<script>
import injection, {trans, formatDate} from '../helpers/injection';
import options from '../helpers/options';

export default {
	beforeRouteEnter(to, from, next) {
		injection.loading.start();
		injection.http.post(`${window.api}backend/system/pam/lists`, {
			type   : 'backend',
			append : 'role'
		}).then(response => {
			const {status, message, data} = response.data;
			if (status) {
				throw new Error(message);
			}
			next(vm => {
				vm.list = data.list;
				vm.pagination = data.pagination;
				vm.roles = data.role;
				injection.loading.finish();
			});
		}).catch((error) => {
			injection.loading.error();
			injection.notice.error({
				title : error,
			});
		});
	},
	data() {
		const that = this;
		// 操作渲染函数
		const handleRender = (h, params) => {
			const btns = [];
			const btnEnable = h('poptip', {
				props : {
					confirm   : true,
					width     : 200,
					title     : '确认启用此用户 ? ',
					placement : 'left'
				},
				on    : {
					'on-ok' : () => {
						that.doItem('enable', params.row.id);
					}
				}
			}, [
				h('i-button', {
					props : {
						type  : 'text',
						size  : 'large',
						shape : 'circle',
					}
				}, [
					h('icon', {
						props : {
							size  : 16,
							type  : 'checkmark-circled',
							color : 'green',
						}
					})
				])
			]);

			const btnDisable = h('i-button', {
				props : {
					type  : 'text',
					size  : 'large',
					shape : 'circle',
				},
				style : {
					marginRight : '5px'
				},
				on    : {
					click : () => {
						that.onDisableBtnClick(params.row.id);
					}
				}
			}, [
				h('icon', {
					props : {
						size  : 16,
						type  : 'eye-disabled',
						color : 'red',
					}
				})
			]);
			if (params.row.can_enable) {
				btns.push(btnEnable);
			}
			if (params.row.can_disable) {
				btns.push(btnDisable);
			}
			// 返回操作按钮
			return h('div', btns);
		};
		return {
			// 用户列表
			list         : [],
			// 分页
			pagination   : {
				page  : 1,
				size  : 20,
				total : 0,
			},
			// 定义的各种选项
			options,
			// 用户列定义
			columns      : [
				{
					title : 'ID',
					key   : 'id'
				},
				{
					title : '用户名',
					key   : 'username'
				},
				{
					title : '手机号',
					key   : 'mobile'
				},
				{
					title : '邮箱',
					key   : 'email'
				},
				{
					title  : '操作',
					key    : 'handle',
					align  : 'center',
					render : handleRender,
				},
			],
			filter       : {
				kw    : '',
				field : '',
				type  : 'backend',
			},
			loading      : false,
			modalItem    : {
				display : false,
				title   : '新建用户',
			},
			modalDisable : {
				display    : false,
				title      : '禁用用户',
				dateOption : {
					disabledDate(date) {
						return date && date.valueOf() < Date.now();
					}
				},
				item       : {
					id     : 0,
					date   : null,
					reason : '',
				}
			},
			disableRules : {
				date   : [
					{
						type     : 'date',
						message  : '日期不能为空',
						required : true,
						trigger  : 'change',
					},
				],
				reason : [
					{
						message  : '原因不能为空',
						required : true,
						trigger  : 'blur',
					},
				],
			},
			item         : {
				passport : '',
				password : '',
				role_id  : 0,
			},
			rules        : {
				passport : [
					{
						message  : '用户名不能为空',
						required : true,
						trigger  : 'blur',
					},
				],
				password : [
					{
						message  : '密码不能为空',
						required : true,
						trigger  : 'blur',
					},
				],
				role_id  : [
					{
						type     : 'number',
						message  : '请选择角色',
						required : true,
						trigger  : 'blur',
					},
				]
			},
			roles        : [],
			dsGuard      : {
				backend : '后台',
				user    : '用户',
				develop : '开发者'
			},
			dsField      : {
				id       : 'ID',
				mobile   : '手机号',
				username : '用户名',
				email    : '邮箱'
			},
		};
	},
	methods : {
		// 刷新界面
		refresh() {
			const self = this;
			self.$loading.start();
			injection.http.post(`${window.api}backend/system/pam/lists`, {
				kw     : self.filter.kw,
				field  : self.filter.field,
				type   : self.filter.type,
				append : 'role',
			}).then(response => {
				const {status, message, data} = response.data;
				if (status) {
					throw new Error(message);
				}
				self.list = data.list;
				self.pagination = data.pagination;
				self.roles = data.role;
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
		search() {
			this.pagination.page = 1;
			this.refresh();
		},
		rowClassName(row) {
			return row.is_enable ? '' : 'liex-row-disable';
		},
		// 分页
		pageChange(page) {
			const self = this;
			self.pagination.page = page;
			self.refresh();
		},
		pageSizeChange(pagesize) {
			const self = this;
			self.pagination.size = pagesize;
			self.refresh();
		},
		/*
		|--------------------------------------------------------------------------
		| 新增数据
		|--------------------------------------------------------------------------
		*/
		// 清空再次点击新增的数据
		resetItem() {
			this.item.passport = '';
			this.item.password = '';
			this.item.role_id = '';
		},
		createItem() {
			// 重置弹框数据
			this.resetItem();
			this.modalItem.display = true;
			this.modalItem.title = '创建用户';
		},
		// 增加角色
		submitItem() {
			const self = this;
			self.$refs.item.validate(valid => {
				if (valid) {
					self.loading = true;
					self.$http.post(
						`${window.api}backend/system/pam/establish`,
						self.item
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
						// 成功关闭页面
						self.modalItem.display = false;
					}).catch(error => {
						self.$notice.error({
							title : error,
						});
					}).finally(() => {
						self.loading = false;
					});
				}
			});
		},
		/*
		|--------------------------------------------------------------------------
		| Do Mutation
		|--------------------------------------------------------------------------
		*/
		doItem(action, id) {
			const self = this;
			self.$http.post(`${window.api}backend/system/pam/do`, {
				action,
				id
			}).then((response) => {
				const {status, message} = response.data;
				if (status) {
					throw new Error(message);
				}
				self.$notice.open({
					title : '操作成功！',
				});
				// 刷新页面
				self.refresh();
			}).catch(error => {
				self.$notice.error({
					title : error,
				});
			});
		},
		onDisableBtnClick(id) {
			this.modalDisable.item.id = id;
			this.modalDisable.display = true;
		},
		clearDisableItem() {
			this.modalDisable.item = {
				id     : 0,
				date   : null,
				reason : '',
			};
			this.modalDisable.display = false;
		},
		disableItem() {
			const self = this;
			self.$refs.disable.validate(valid => {
				if (valid) {
					self.loading = true;
					const {item} = self.modalDisable;
					const variable = Object.assign({}, item);
					variable.date = formatDate(variable.date);
					self.$http.post(
						`${window.api}backend/system/pam/disable`,
						variable
					).then((response) => {
						const {status, message} = response.data;
						if (status) {
							throw new Error(message);
						}
						self.$notice.open({
							title : '操作成功！',
						});
						self.refresh();
					}).catch(() => {
						self.$notice.error({
							title : '操作失败',
						});
					}).finally(() => {
						self.clearDisableItem();
						self.loading = false;
					});
				}
			});
		},
	},
	mounted() {
		this.$store.commit('title', trans('用户账号管理'));
	},
};
</script>

<template>
	<card :bordered="false">
		<p slot="title">用户账号管理</p>
		<i-button slot="extra" type="info" size="small" @click.native="createItem">
			<icon type="android-add"></icon>
			新增
		</i-button>
		<i-form inline>
			<form-item prop="field">
				<i-select placeholder="账号类型" v-model="filter.field" clearable>
					<i-option v-for="(label, index) in dsField" :value="index" :key="index">{{ label }}
					</i-option>
				</i-select>
			</form-item>
			<form-item prop="kw">
				<i-input type="text" placeholder="关键词" v-model="filter.kw" clearable></i-input>
			</form-item>
			<form-item prop="type">
				<i-select placeholder="账号类型" v-model="filter.type">
					<i-option v-for="(label, index) in dsGuard" :value="index" :key="index">{{ label }}
					</i-option>
				</i-select>
			</form-item>
			<i-button class="btn-action" type="primary" @click.native="search">
				<icon type="search"></icon>
				搜索
			</i-button>
		</i-form>
		<i-table
				:row-class-name="rowClassName"
				:columns="columns" :data="list"></i-table>
		<page show-sizer show-elevator
			  :current="pagination.page"
			  :total="pagination.total"
			  :page-size="pagination.size" class-name="liex-pager"
			  @on-change="pageChange"
			  @on-page-size-change="pageSizeChange"></page>

		<!--用户创建-->
		<modal v-model="modalItem.display" class="liex-modal-delete"
			   :title="modalItem.title">
			<i-form ref="item" :model="item" :rules="rules" :label-width="110">
				<form-item label="用户名" prop="passport">
					<i-input v-model="item.passport"></i-input>
				</form-item>
				<form-item label="密码" prop="password">
					<i-input v-model="item.password"></i-input>
				</form-item>
				<form-item label="用户角色" prop="role_id">
					<i-select placeholder="选择用户角色" v-model="item.role_id">
						<i-option v-for="item in roles" :value="item.id" :key="item.id">{{ item.title }}
						</i-option>
					</i-select>
				</form-item>
				<form-item>
					<i-button :loading="loading" @click.native="submitItem"
							  class="btn-group" type="success">
						<span v-if="!loading">创建用户</span>
						<span v-else>正在提交…</span>
					</i-button>
				</form-item>
			</i-form>
		</modal>

		<modal v-model="modalDisable.display" class="liex-modal-delete"
			   :title="modalDisable.title">
			<i-form ref="disable" :model="modalDisable.item" :rules="disableRules" :label-width="110">
				<form-item label="解禁时间" prop="date">
					<date-picker
							v-model="modalDisable.item.date"
							type="date" :options="modalDisable.dateOption"
							placeholder="选择解禁时间" style="width: 130px"></date-picker>
				</form-item>
				<form-item label="禁用原因" prop="reason">
					<i-input v-model="modalDisable.item.reason"
							 type="textarea" :autosize="{minRows: 2,maxRows: 5}"
							 placeholder="输入禁用原因"></i-input>
				</form-item>
				<form-item>
					<i-button :loading="loading" @click.native="disableItem"
							  class="btn-group" type="error">
						<span v-if="!loading">禁用用户</span>
						<span v-else>正在提交…</span>
					</i-button>
				</form-item>
			</i-form>
		</modal>
	</card>
</template>