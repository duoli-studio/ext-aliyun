<script>
import injection from '../helpers/injection';

export default {
	beforeRouteEnter(to, from, next) {
		injection.loading.start();
		injection.http.post(`${window.api}backend/system/category/lists `, {
			parent_id : 0
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
					}
				}
			});
			btns.push(btnEdit);
			btns.push(btnDelete);
			// 返回操作按钮
			return h('div', btns);
		};
		return {
			// 查询条件
			filter      : {
				id        : '',
				type      : '',
				parent_id : 0,
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
					width : 75,
					title : 'ID',
					key   : 'id'
				},
				{
					title : '标题',
					key   : 'title',
				},
				{
					title : '类型',
					key   : 'type',
				},
				{
					title  : '操作',
					key    : 'handle',
					align  : 'right',
					render : handleRender,
				},
			],
			c2e         : {
				display    : false,
				loading    : false,
				type       : 'create',
				data       : {
					type      : '',
					parent_id : '',
					title     : '',
				},
				rules      : {
					parent_id : [
						{
							message  : '父级ID不能为空',
							required : true,
							trigger  : 'blur'
						},
					],
					title     : [
						{
							message  : '标题不能为空',
							required : true,
							trigger  : 'blur',
						},
					],
					type      : [
						{
							message  : '类型不能为空',
							required : true,
							trigger  : 'blur',
						},
					],
				},
				dataSource : {
					help     : '帮助',
					activity : '活动'
				},
				dsSource   : {
					help     : '帮助',
					activity : '活动'
				}
			},
		};
	},
	methods : {
		c2eReset() {
			this.c2e.data.id = 0;
			this.c2e.data.title = '';
			this.c2e.data.parent_id = null;
			this.c2e.type = 'create';
		},
		// 增加
		onC2eSubmit() {
			const self = this;
			self.$refs.c2e.validate(valid => {
				if (valid) {
					self.c2e.loading = true;
					self.$http.post(
						`${window.api}backend/system/category/establish`,
						self.c2e.data,
					).then(() => {
						self.$notice.open({
							title : '保存成功！',
						});
						// 重置弹窗
						self.c2eReset();
						// 刷新页面
						self.refresh();
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
		// 编辑
		onC2eEditClick(params) {
			this.c2e.type = 'edit';
			this.c2e.title = '编辑';
			this.c2e.display = true;
			this.c2e.data.type = params.row.type;
			this.c2e.data.title = params.row.title;
			this.c2e.data.parent_id = params.row.parent_id.toString();
		},
		// 删除
		onDoClick(params) {
			const self = this;
			self.loading = true;
			injection.http.post(`${window.api}backend/system/category/do`, {
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
		// 刷新
		refresh() {
			const self = this;
			self.$loading.start();
			injection.http.post(`${window.api}backend/system/category/lists`, {
				id        : self.filter.id,
				type      : self.filter.type,
				parent_id : self.filter.parent_id,
				page      : self.pagination.page,
				size      : self.pagination.size,
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
		onC2eAdd() {
			this.c2eReset();
			this.c2e.display = true;
		},
	},
	mounted() {
		this.$store.commit('title', '帮助分类');
	},
};
</script>
<template>
	<card :bordered="false">
		<p slot="title">
			帮助分类
		</p>
		<i-button slot="extra" type="info" size="small" @click.native="onC2eAdd">
			<icon type="android-add"></icon>
			新增
		</i-button>
		<i-form inline>
			<form-item prop="user">
				<i-input type="text" placeholder="ID" v-model="filter.id"></i-input>
			</form-item>
			<form-item prop="parent_id">
				<i-select v-model="filter.type" style="width:150px;" placeholder="选择类型" clearable>
					<i-option v-for="(value ,type) in c2e.dataSource" :value="type" :key="type">{{ value }}</i-option>
				</i-select>
			</form-item>
			<i-button class="btn-action" type="primary" @click.native="search">
				<icon type="search"></icon>
				搜索
			</i-button>
		</i-form>
		<i-table :columns="listColumns" :data="list"></i-table>
		<!--c2e modal-->
		<modal v-model="c2e.display" class="liex-modal-delete"
		       :title="c2e.type==='create'? '创建' : '编辑'">
			<i-form ref="c2e" :model="c2e.data" :rules="c2e.rules" :label-width="110">
				<form-item label="类型" prop="type">
					<i-select v-model="c2e.data.type" style="width:150px;" placeholder="选择类型" clearable>
						<i-option v-for="(value ,type) in c2e.dsSource" :value="type" :key="type">{{ value }}
						</i-option>
					</i-select>
				</form-item>
				<form-item label="父级ID" prop="parent_id">
					<i-input placeholder="0为顶级,1为0的子集" v-model="c2e.data.parent_id"></i-input>
				</form-item>
				<form-item label="标题" prop="title">
					<i-input v-model="c2e.data.title"></i-input>
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