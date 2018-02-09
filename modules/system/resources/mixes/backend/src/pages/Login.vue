<script>
export default {
	data() {
		return {
			form    : {
				password : '',
				username : '',
			},
			loading : false,
			rules   : {
				password : [
					{
						required : true,
						type     : 'string',
						message  : '请输入密码',
						trigger  : 'change',
					},
				],
				username : [
					{
						required : true,
						type     : 'string',
						message  : '请输入账号',
						trigger  : 'change',
					},
				],
			},
		};
	},
	methods : {
		submit() {
			const self = this;
			self.loading = true;
			self.$refs.form.validate(valid => {
				if (valid) {
					self.$http.post(`${window.api}pam/auth/token/backend`, {
						passport : self.form.username,
						password : self.form.password,
					}).then(response => {
						self.$notice.open({
							title : '恭喜，登录成功！',
						});
						self.$notice.open({
							title : '即将跳转...',
						});
						const {token} = response.data;
						self.$store.commit('token', token);
						window.location = window.admin;
					}).finally(() => {
						self.loading = false;
					});
				}
				else {
					self.loading = false;
					self.$notice.error({
						title : '请正确填写账号或密码！',
					});
				}
			});
		},
	},
};
</script>
<template>
	<div class="login-layout" @keyup.enter="submit">
		<div class="background one"></div>
		<div class="background two"></div>
		<div class="background three"></div>
		<div class="background four"></div>
		<div class="background five"></div>
		<div class="background six"></div>
		<i-form :model="form" :label-width="0" ref="form" :rules="rules">
			<div class="title">管理后台</div>
			<form-item label="" prop="username">
				<i-input icon="person" v-model="form.username" placeholder="请输入账号"></i-input>
			</form-item>
			<form-item label="" prop="password">
				<i-input icon="locked" type="password" v-model="form.password" placeholder="请输入密码"></i-input>
			</form-item>
			<form-item>
				<i-button :loading="loading" size="large" type="primary" @click.native="submit">
					<span v-if="!loading">登录</span>
					<span v-else>正在提交…</span>
				</i-button>
			</form-item>
		</i-form>
	</div>
</template>