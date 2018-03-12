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
		injection.http.post(`${window.api}backend/system/area/lists`, {
			parent_id : 0,
			page      : 1,
			append    : 'parent'
		}).then(response => {
			const {status, message, data} = response.data;
			if (status) {
				throw new Error(message);
			}
			next(vm => {
				vm.list.items = data.list;
				vm.list.pagination = data.pagination;
				vm.dataSource.parent = data.parent;
				injection.loading.finish();
			});
		}).catch(() => {
			injection.$notice.error({
				title : '加载失败',
			});
		});
	},
	data() {
		const that = this;
		// 操作渲染函数
		const handleRender = (h, params) => {
			const btns = [];
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
					title     : '确认删除此地区 ? ',
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
				})
			]);
			btns.push(btnEdit);
			btns.push(btnDelete);
			// 返回操作按钮
			return h('div', btns);
		};
		return {
			list       : {
				items      : [],
				pagination : {
					page  : 1,
					size  : 20,
					total : 0,
				},
				filter     : {
					parent_id : 0,
				},
				columns    : [
					{
						width : 75,
						title : '地区ID',
						key   : 'id'
					},
					{
						title : '名称',
						key   : 'title'
					},
					{
						title  : '操作',
						key    : 'handle',
						align  : 'right',
						render : handleRender,
					},
				]
			},
			c2e        : {
				display : false,
				loading : false,
				type    : 'create',
				data    : {
					id        : 0,
					parent_id : '',
					title     : '',
				},
				rules   : {
					title : [
						{
							message  : '地区名称不能为空',
							required : true,
							trigger  : 'blur',
						},
					],
				},
			},
			do         : {
				loading : false,
			},
			dataSource : {
				parent : []
			},
		};
	},
	methods : {
		// 清空再次点击新增的数据
		c2eReset() {
			this.c2e.data.id = 0;
			this.c2e.data.title = '';
			this.c2e.data.parent_id = '';
			this.c2e.type = 'create';
		},
		// 删除数据
		onDo(params, action) {
			const self = this;
			self.do.loading = true;
			injection.http.post(`${window.api}backend/system/area/do`, {
				action,
				id : params.row.id
			}).then((response) => {
				const {status, message} = response.data;
				if (status) {
					throw new Error(message);
				}
				self.$notice.open({
					title : '操作成功！',
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
		// 点击新增的弹出框
		onC2eAdd() {
			// 重置弹框数据
			this.c2eReset();
			this.c2e.display = true;
		},
		onC2eEdit(params) {
			// 重置弹框数据
			this.c2e.data.id = params.row.id;
			this.c2e.data.title = params.row.title;
			this.c2e.data.parent_id = params.row.parent_id;
			this.c2e.type = 'edit';
			this.c2e.display = true;
		},
		// 增加地区
		onC2eSubmit() {
			const self = this;
			self.$refs.c2e.validate(valid => {
				if (valid) {
					self.c2e.loading = true;
					self.$http.post(
						`${window.api}backend/system/area/establish`,
						self.c2e.data,
					).then(() => {
						self.$notice.open({
							title : '保存成功！',
						});
						// 重置弹窗
						self.c2eReset();
						// 刷新页面
						self.listRefresh();
						self.c2e.display = false;
					}).catch(() => {
						self.$notice.error({
							title : '保存失败！',
						});
					}).finally(() => {
						self.c2e.loading = false;
					});
				}
			});
		},
		// 查找
		listSearch() {
			this.list.pagination.page = 1;
			this.listRefresh();
		},
		// 刷新界面
		listRefresh() {
			const self = this;
			self.$loading.start();
			injection.http.post(`${window.api}backend/system/area/lists`, {
				parent_id : self.list.filter.parent_id,
				page      : self.list.pagination.page,
				size      : self.list.pagination.size,
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
		this.$store.commit('title', trans('地区管理'));
	},
};
</script>
<template>
	<card :bordered="false">
		<p slot="title">地区管理</p>
		<i-button slot="extra" type="info" size="small"
				  @click.native="onC2eAdd">
			<icon type="android-add"></icon>
			新增
		</i-button>
		<i-form inline>
			<form-item prop="parent_id">
				<i-select v-model="list.filter.parent_id" style="width:150px;" placeholder="选择父级" clearable filterable>
					<i-option v-for="item in dataSource.parent" :value="item.key" :label="item.value"
							  :key="item.key"></i-option>
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
		<!--新增弹出框start-->
		<modal v-model="c2e.display" class="liex-modal-delete"
			   :title="c2e.type==='create'? '创建地区' : '编辑地区'">
			<i-form ref="c2e" :model="c2e.data" :rules="c2e.rules" :label-width="110">
				<form-item label="地区名称" prop="title">
					<i-input v-model="c2e.data.title"></i-input>
				</form-item>
				<form-item label="上级地区" prop="parent_id">
					<i-select v-model="c2e.data.parent_id" style="width:150px;" placeholder="选择父级" clearable filterable>
						<i-option v-for="item in dataSource.parent" :value="item.key" :label="item.value"
								  :key="item.key"></i-option>
					</i-select>
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