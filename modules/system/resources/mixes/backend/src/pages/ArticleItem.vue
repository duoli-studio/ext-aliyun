<script>
export default {
	data() {
		// 操作渲染函数
		return {
			c2e        : {
				display : false,
				loading : false,
				type    : 'create',
				data    : {
					title   : '',
					cat_id  : '',
					content : '5'
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
		<p slot="title">帮助详细</p>
		<i-form ref="c2e" :model="c2e.data" :rules="c2e.rules" :label-width="110">
			<form-item label="分类ID" prop="cat_id">
				<i-input v-model="c2e.data.cat_id"></i-input>
			</form-item>
			<form-item label="标题" prop="title">
				<i-input v-model="c2e.data.title"></i-input>
			</form-item>
			<form-item label="内容" prop="content">
				<editor v-model="c2e.data.content"></editor>
			</form-item>
			<form-item>
				<i-button :loading="c2e.loading" @click.native="onC2eSubmit"
						  class="btn-group" type="success">
					<span v-if="!c2e.loading">{{c2e.type === 'create' ? '创建' : '编辑'}}</span>
					<span v-else>正在提交…</span>
				</i-button>
			</form-item>
		</i-form>
	</card>
</template>