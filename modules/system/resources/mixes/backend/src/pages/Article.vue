<script>
import injection from '../helpers/injection';

export default {
	beforeRouteEnter(to, from, next) {
		injection.loading.start();
		injection.http.post(`${window.api}backend/system/help/lists`, {
			page   : 1,
			append : 'category'
		}).then(response => {
			const {status, message, data} = response.data;
			if (status) {
				throw new Error(message);
			}
			next(vm => {
				vm.list.items = data.list;
				vm.list.pagination = data.pagination;
				vm.dataSource.category = data.category;
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
			const btnDelete = h('poptip', {
				props : {
					confirm   : true,
					width     : 200,
					title     : '确认删除 ? ',
					placement : 'left'
				},
				on    : {
					'on-ok' : () => {
						that.onDoClick(params);
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

			const btnEdit = h('i-button', {
				props : {
					type  : 'primary',
					size  : 'small',
					icon  : 'edit',
					shape : 'circle',
				},
				style : 'margin-right:5px;',
				on    : {
					click : () => {
						that.onC2eEditClick(params);
						console.log(params);
					}
				}
			});
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
					cat_id  : '',
					title   : '',
					content : '',
				},
				columns    : [
					{
						width : 75,
						title : 'ID',
						key   : 'id'
					},
					{
						title : '分类ID',
						key   : 'cat_title'
					},
					{
						title : '分类标题',
						key   : 'title',
					},
					{
						title : '帮助内容',
						key   : 'content',
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
				loading : false,
				type    : 'create',
				data    : {
					title   : '',
					cat_id  : '',
					content : '',
				},
				rules   : {
					cat_id  : [
						{
							message  : 'ID不能为空',
							required : true,
							trigger  : 'blur',
						},
					],
					title   : [
						{
							message  : '分类标题不能为空',
							required : true,
							trigger  : 'blur',
						},
					],
					content : [
						{
							message  : '内容不能为空',
							required : true,
							trigger  : 'blur',
						},
					],
				},
			},
			dataSource : {
				category : [],
			}
		};
	},
	methods : {
		onC2eReset() {
			this.c2e.data.cat_id = null;
			this.c2e.data.title = '';
			this.c2e.data.content = '';
			this.c2e.type = 'create';
		},
		// 新增分类
		onC2eSubmit() {
			const self = this;
			self.$refs.c2e.validate(valid => {
				if (valid) {
					self.c2e.loading = true;
					self.$http.post(
						`${window.api}backend/system/help/establish`,
						self.c2e.data,
					).then((response) => {
						const {status, message} = response.data;
						if (status) {
							throw new Error(message);
						}
						self.$notice.open({
							title : '保存成功！',
						});
						// 重置弹窗
						self.onC2eReset();
						// 刷新页面
						self.refresh();
					}).catch(() => {
						self.$notice.error({
							title : '保存失败！',
						});
					}).finally(() => {
						self.c2e.loading = false;
						self.c2e.display = false;
					});
				}
			});
		},
		// 删除
		onDoClick(params) {
			const self = this;
			self.loading = true;
			injection.http.post(`${window.api}backend/system/help/do`, {
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
		// 编辑
		onC2eEditClick(params) {
			this.c2e.type = 'edit';
			this.c2e.title = '编辑';
			this.c2e.display = true;
			this.c2e.data.id = params.row.id;
			this.c2e.data.title = params.row.title;
			this.c2e.data.cat_id = params.row.cat_id.toString();
			this.c2e.data.content = params.row.content;
		},
		// 刷新页面
		refresh() {
			const self = this;
			self.$loading.start();
			injection.http.post(`${window.api}backend/system/help/lists`, {
				cat_id : self.filter.cat_id,
				page   : self.list.pagination.page,
				size   : self.list.pagination.size,
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
			}).catch(() => {
				self.$loading.error();
				self.$notice.error({
					title : '刷新数据失败！',
				});
			});
			this.loading = false;
		},
		// 查找
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
		onC2eAdd() {
			this.onC2eReset();
			this.c2e.display = true;
		},
	},
	mounted() {
		this.$store.commit('title', '帮助列表');
	},
};
</script>
<template>
	<card :bordered="false">
		<p slot="title">
			帮助列表
		</p>
		<i-button slot="extra" type="info" size="small" @click.native="onC2eAdd">
			<icon type="android-add"></icon>
			新增
		</i-button>
		<i-form inline>
			<form-item prop="cat_id">
				<i-select v-model="list.filter.cat_id" style="width:150px;" placeholder="选择分类" clearable>
					<i-option v-for="item in dataSource.category" :value="item.key" :label="item.value"
							  :key="item.key"></i-option>
				</i-select>
			</form-item>
			<i-button class="btn-action" type="primary" @click.native="listSearch">
				<icon type="search"></icon>
				搜索
			</i-button>
		</i-form>
		<i-table :columns="list.columns" :data="list.items"></i-table>
		<!--c2e-->
		<modal v-model="c2e.display" class="liex-modal-delete"
			   :title="c2e.type==='create'? '创建' : '编辑'">
			<i-form ref="c2e" :model="c2e.data" :rules="c2e.rules" :label-width="110">
				<form-item label="分类ID" prop="cat_id">
					<i-input v-model="c2e.data.cat_id"></i-input>
				</form-item>
				<form-item label="标题" prop="title">
					<i-input v-model="c2e.data.title"></i-input>
				</form-item>
				<form-item label="内容" prop="content">
					<i-input v-model="c2e.data.content"></i-input>
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

		<!--pagination-->
		<page show-sizer show-elevator
			  :current="list.pagination.page"
			  :total="list.pagination.total"
			  :page-size="list.pagination.size" class-name="liex-pager"
			  @on-change="listPageChange"
			  @on-page-size-change="listPageSizeChange"
		></page>
	</card>
</template>