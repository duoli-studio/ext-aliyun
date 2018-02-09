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
		injection.http.post(`${window.api}backend/order/dan/lists`, {
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
			const btns = [];
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
					title     : '确认删除此段位 ? ',
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
			btns.push(btnEdit);
			btns.push(btnDelete);
			// 返回操作按钮
			return h('div', btns);
		};

		return {
			// 角色列表
			list        : [],
			// 分页
			pagination  : {
				page  : 1,
				size  : 15,
				total : 0,
			},
			// 段位列定义
			listColumns : [
				{
					title : 'ID',
					key   : 'id'
				},
				{
					title : '游戏ID',
					key   : 'game_id'
				},
				{
					title : '段位名称',
					key   : 'title'
				},
				{
					title : '游戏名称',
					key   : 'game_title'
				},
				{
					title  : '操作',
					key    : 'handle',
					align  : 'center',
					render : handleRender,
				},
			],
			modalItem   : {
				display : false,
				title   : '创建段位',
				type    : 'create',
				loading : false
			},
			loading     : false,
			rules       : {
				title : [
					{
						message  : '段位不能为空',
						required : true,
						trigger  : 'blur',
					},
				],
			},
			item        : {
				id          : 0,
				name        : '',
				title       : '',
				guard       : '',
				description : '',
			},
			// 新增角色类型
//                guardList         : [
//                    {
//                        value : 'backend',
//                        label : '后台',
//                    },
//                    {
//                        value : 'web',
//                        label : '用户',
//                    },
//                    {
//                        value : 'develop',
//                        label : '开发者',
//                    },
//                ],
		};
	},
	methods : {
		// 清空再次点击新增的数据
		resetItem() {
			this.item.id = 0;
			this.item.title = '';
			this.item.game_id = '';
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
			injection.http.post(`${window.api}backend/order/dan/do`, {
					action : 'delete',
					id     : params.row.id
			}).then(() => {
				self.$notice.open({
					title : '删除数据成功！',
				});
				self.refresh();
			}).catch(() => {
			}).finally(() => {
				self.loading = false;
			});
		},
		removeItem(params) {
			const self = this;
			self.loading = true;
			injection.http.post(`${window.api}backend/order/dan/do`, {
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
			this.modalItem.title = '创建段位';
		},
		// 刷新页面
		refresh() {
			const self = this;
			self.$loading.start();
			injection.http.post(`${window.api}backend/order/dan/lists`, {
				game_id : self.filter.game_id,
				page    : self.pagination.page,
				size    : self.pagination.size,
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
		// 增加段位
		submitItem() {
			const self = this;
			let variable = {};
			if (self.modalItem.type === 'create') {
				variable = {
					game_id      : self.item.game_id,
					name         : self.item.name,
					title        : self.item.title,
					guard        : self.item.guard,
					hunter_grade : self.item.hunter_grade,
					hunter_type  : self.item.hunter_type,
				};
			}
			else {
				variable = {
					game_id      : self.item.game_id,
					name         : self.item.name,
					title        : self.item.title,
					guard        : self.item.guard,
					hunter_grade : self.item.hunter_grade,
					hunter_type  : self.item.hunter_type,
					id           : self.item.name,
				};
			}
			self.$refs.item.validate(valid => {
				if (valid) {
					self.loading = true;
					self.$http.post(
						`${window.api}backend/order/dan/establish`,
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
		// 编辑段位
		fetchRole(params) {
			this.modalItem.display = true;
			this.modalItem.type = 'edit';
			this.item.title = params.row.title;
			this.item.game_id = params.row.game_id;
		},
		// 查找
//            search() {
//                this.pagination.page = 1;
//                this.refresh();
//            },
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
		this.$store.commit('title', trans('段位管理'));
	},
};
</script>
<template>
	<card :bordered="false">
		<p slot="title">段位管理</p>
		<i-button slot="extra" type="info" size="small"
		          @click.native="createItem">
			<icon type="android-add"></icon>
			新增
		</i-button>
		<!--<i-form inline>-->
		<!--<form-item prop="user">-->
		<!--<i-input type="text" placeholder="关键词" v-model="listFilter.keyword"></i-input>-->
		<!--</form-item>-->
		<!--<form-item prop="type">-->
		<!--<i-select placeholder="用户类型" v-model="listFilter.type">-->
		<!--<i-option v-for="item in guardList" :value="item.value" :key="item.value">{{ item.label }}-->
		<!--</i-option>-->
		<!--</i-select>-->
		<!--</form-item>-->
		<!--<i-button class="btn-action" type="primary" @click.native="search">-->
		<!--<icon type="search"></icon>-->
		<!--搜索-->
		<!--</i-button>-->
		<!--</i-form>-->

		<i-table :columns="listColumns" :data="list"></i-table>
		<page show-sizer show-elevator
		      :current="pagination.page"
		      :page-size-opts="options.pageSize"
		      :total="pagination.total"
		      :page-size="pagination.size" class-name="liex-pager"
		      @on-change="pageChange"
		      @on-page-size-change="pageSizeChange"
		></page>
		<!--新增弹出框start-->
		<modal v-model="modalItem.display" class="liex-modal-delete"
		       :title="modalItem.title">
			<i-form ref="item" :model="item" :rules="rules" :label-width="110">
				<form-item label="游戏ID" prop="title">
					<i-input v-model="item.game_id"></i-input>
				</form-item>
				<form-item label="段位名称" prop="title">
					<i-input v-model="item.title"></i-input>
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