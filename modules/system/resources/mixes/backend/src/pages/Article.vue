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
				}
			],
			// dsField     : {
			// 	id         : 'ID',
			// 	account_id : '账户ID',
			// 	charge_no  : '提现流水号',
			// },
			// dsType      : {
			// 	ali  : '提现到支付宝',
			// 	wx   : '提现到微信',
			// 	bank : '提现到银行卡',
			// },

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
		// 增加分类
		onC2eSubmit() {
			const self = this;
			const variable = {
				cat_id  : self.c2e.data.cat_id,
				title   : self.c2e.data.title,
				content : self.c2e.data.content,
			};
			// if (self.c2e.type !== 'create') {
			// 	variable['id'] = self.c2e.data.id;
			// }

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
		<!--<i-form inline>-->
		<!--<form-item prop="field">-->
		<!--<i-select placeholder="查询类型" v-model="filter.field" clearable>-->
		<!--<i-option v-for="(value ,type) in dsField" :value="type" :key="type">{{ value }}</i-option>-->
		<!--</i-select>-->
		<!--</form-item>-->
		<!--<form-item prop="kw">-->
		<!--<i-input type="text" placeholder="关键词" v-model="filter.kw"></i-input>-->
		<!--</form-item>-->
		<!--<form-item prop="type">-->
		<!--<i-select placeholder="提现类型" v-model="filter.type" clearable style="width:200px">-->
		<!--<i-option v-for="(value ,type) in dsType" :value="type" :key="type">{{ value }}</i-option>-->
		<!--</i-select>-->
		<!--</form-item>-->
		<!--<form-item prop="type">-->
		<!--<date-picker type="daterange" @on-change="onDateRangeChange" placement="bottom-end" placeholder="选择开始时间"-->
		<!--style="width: 180px"></date-picker>-->
		<!--</form-item>-->
		<!--<i-button class="btn-action" type="primary" @click.native="search">-->
		<!--<icon type="search"></icon>-->
		<!--搜索-->
		<!--</i-button>-->
		<!--</i-form>-->
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