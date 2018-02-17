<script>
import injection from '../helpers/injection';

export default {
	beforeRouteEnter(to, from, next) {
		injection.loading.start();
		injection.http.post(`${window.api}backend/system/help/lists`, {
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
			const btnDelete = h('poptip', {
				props : {
					confirm   : true,
					width     : 200,
					title     : '确认删除此地区 ? ',
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
			btns.push(btnDelete);
			// 返回操作按钮
			return h('div', btns);
		};
		return {
			// 查询条件
			filter      : {
				field    : '',
				kw       : '',
				type     : '',
				status   : '',
				start_at : '',
				end_at   : '',
			},
			// 提现记录列表
			list        : [],
			// 分页
			pagination  : {
				page  : 1,
				size  : 20,
				total : 0,
			},
			listColumns : [
				{
					title : 'ID',
					key   : 'id'
				},
				{
					title : '分类ID',
					key   : 'cat_id'
				},
				{
					title : '分类标题',
					key   : 'title'
				},
				{
					title : '帮助内容',
					key   : 'content'
				},
				{
					title  : '操作',
					key    : 'handle',
					align  : 'center',
					render : handleRender,
				},
			],
			c2e : {
				display : false,
				loading : false,
				title   : '创建分类',
				type    : 'create',
				data    : {
					// id      : 0,
					title   : '',
					cat_id  : '',
					content : '',
				},
				rules   : {
					cat_id  : [
						{
							message  : '分类ID不能为空',
							required : true,
							trigger  : 'blur',
						},
					],
					title : [
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
			}
		};
	},
	methods : {
		// 初始值
		onC2eReset() {
			this.c2e.data.cat_id = '';
			this.c2e.data.title = '';
			this.c2e.data.content = '';
		},
		// 新增分类
		onC2eSubmit() {
			const self = this;
			const variable = {
				cat_id  : self.c2e.data.cat_id,
				title   : self.c2e.data.title,
				content : self.c2e.data.content,
			};
			self.$refs.c2e.validate(valid => {
				if (valid) {
					self.c2e.loading = true;
					self.$http.post(
						`${window.api}backend/system/help/establish`,
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
		// 删除分类
		removeItem(params) {
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
		// 刷新页面
		refresh() {
			const self = this;
			self.$loading.start();
			injection.http.post(`${window.api}backend/system/help/lists`, {
				field    : self.filter.field,
				kw       : self.filter.kw,
				type     : self.filter.type,
				start_at : self.filter.start_at,
				end_at   : self.filter.end_at,
				page     : self.pagination.page,
				size     : self.pagination.size,
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
			}).catch(() => {
				self.$loading.error();
				self.$notice.error({
					title : '刷新数据失败！',
				});
			});
			this.loading = false;
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
		},
		onDateRangeChange(e) {
			const self = this;
			const startAt = e[0];
			const endAt = e[1];
			self.filter.start_at = startAt;
			self.filter.end_at = endAt;
		},
		onC2eAdd() {
			// 重置弹框数据
			this.onC2eReset();
			// this.c2e.data.parent_id = 0;
			// this.c2e.display = true;
			// this.c2e.title = '创建';
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
		<i-table :columns="listColumns" :data="list"></i-table>
		<!--c2e-->
		<modal v-model="c2e.display" class="liex-modal-delete"
		       :title="c2e.title">
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
		<!--分页-->
		<page show-sizer show-elevator
		      :current="pagination.page"
		      :total="pagination.total"
		      :page-size="pagination.size" class-name="liex-pager"
		      @on-change="pageChange"
		      @on-page-size-change="pageSizeChange"></page>
	</card>
</template>