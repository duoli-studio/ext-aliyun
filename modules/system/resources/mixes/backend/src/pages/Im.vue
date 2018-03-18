<script>
import injection, {trans} from '../helpers/injection';

const imQuery = `
query {
	systemAccount:be_setting(key:"system::im_notice.system_account"){
		key,value
	},
	systemImage:be_setting(key:"system::im_notice.system_image"){
		key,value
	},
	noticeAccount:be_setting(key:"system::im_notice.notice_account"){
		key,value
	},
	noticeImage:be_setting(key:"system::im_notice.notice_image"){
		key,value
	},
	orderAccount:be_setting(key:"system::im_notice.order_account"){
		key,value
	},
	orderImage:be_setting(key:"system::im_notice.order_image"){
		key,value
	},
}`;

export default {
	beforeRouteEnter(to, from, next) {
		injection.loading.start();
		injection.loading.start();
		injection.http.post(`${window.url}/api/g/backend`, {
			query : imQuery,
		}).then(response => {
			const {
				systemAccount,
				systemImage,
				noticeAccount,
				noticeImage,
				orderAccount,
				orderImage
			} = response.data.data;
			next(vm => {
				vm.form.system = systemAccount.value;
				vm.form.notice = noticeAccount.value;
				vm.form.order = orderAccount.value;
				vm.form.system_icon = systemImage.value;
				vm.form.notice_icon = noticeImage.value;
				vm.form.order_icon = orderImage.value;

				if (systemImage.value) {
					vm.fileList.system = [
						{
							name : '',
							url  : systemImage.value
						}
					];
				}
				if (noticeImage.value) {
					vm.fileList.notice = [
						{
							name : '',
							url  : noticeImage.value
						}
					];
				}
				if (orderImage.value) {
					vm.fileList.order = [
						{
							name : '',
							url  : orderImage.value
						}
					];
				}
				injection.loading.finish();
			});
		}).catch(() => {
			injection.loading.error();
		});
	},
	data() {
		return {
			form       : {
				system      : '',
				notice      : '',
				order       : '',
				system_icon : '',
				notice_icon : '',
				order_icon  : '',
			},
			loading    : false,
			modal      : {
				loading : true,
				visible : false,
			},
			notice     : {
				type     : 'system',
				msg_type : 'single',
				passport : '',
				content  : '这是一条测试消息！',
			},
			rules      : {
				system : [
					{
						required : true,
						type     : 'string',
						message  : '系统通知账号',
						trigger  : 'change',
					},
				],
				notice : [
					{
						required : true,
						type     : 'string',
						message  : '官方通知账号',
						trigger  : 'change',
					},
				],
				order  : [
					{
						required : true,
						type     : 'string',
						message  : '订单通知账号',
						trigger  : 'change',
					},
				],
			},
			testRules  : {
				type     : [
					{
						required : true,
						type     : 'string',
						message  : '推送类型',
						trigger  : 'change',
					},
				],
				msg_type : [
					{
						required : true,
						type     : 'string',
						message  : '消息类型',
						trigger  : 'change',
					},
				],
				content  : [
					{
						required : true,
						type     : 'string',
						message  : '接收消息的用户名',
						trigger  : 'change',
					},
				],
			},
			fileList   : {
				system : [],
				notice : [],
				order  : [],
			},
			dataSource : {
				types    : {
					system : '系统通知',
					notice : '官方公告',
					order  : '订单消息'
				},
				msgTypes : {
					single : '单发',
					multi  : '群发',
				}
			}
		};
	},
	methods : {
		submit() {
			const self = this;
			self.loading = true;
			self.$refs.form.validate(valid => {
				if (valid) {
					self.$http.post(`${window.url}/api_v1/backend/system/im/set`, {
						system      : self.form.system,
						system_icon : self.form.system_icon,
						notice      : self.form.notice,
						notice_icon : self.form.notice_icon,
						order       : self.form.order,
						order_icon  : self.form.order_icon,
					}).then(() => {
						self.$notice.open({
							title : '更新上传配置信息成功！',
						});
						self.$store.dispatch('setting');
					}).finally(() => {
						self.loading = false;
					});
				}
				else {
					self.loading = false;
					self.$notice.error({
						title : '请正确填写上传配置信息！',
					});
				}
			});
		},
		systemIconSuccess(list) {
			this.form.system_icon = list[0].url;
		},
		systemIconRemove() {
			this.form.system_icon = '';
		},
		noticeIconSuccess(list) {
			this.form.notice_icon = list[0].url;
		},
		noticeIconRemove() {
			this.form.notice_icon = '';
		},
		orderIconSuccess(list) {
			this.form.order_icon = list[0].url;
		},
		orderIconRemove() {
			this.form.order_icon = '';
		},
		send() {
			const self = this;
			self.modal.loading = true;
			self.$refs.sendForm.validate(valid => {
				if (valid) {
					self.$http.post(`${window.api}backend/system/im/send`, self.notice).then((response) => {
						const {status, message} = response.data;
						if (status) {
							throw new Error(message);
						}
						self.$notice.open({
							title : '发送测试消息成功！内容如下：',
							desc  : self.notice.content,
						});
						self.modal.visible = false;
					}).catch((e) => {
						self.$notice.error({
							title : '发送失败！',
							desc  : e,
						});
						self.modal.loading = false;
					}).finally(() => {
						self.modal.loading = true;
					});
				}
				else {
					self.modal.loading = false;
					window.setTimeout(() => {
						self.modal.loading = true;
					}, 10);
				}
			});
		},
	},
	mounted() {
		this.$store.commit('title', trans('系统通知'));
	},
};
</script>
<template>
	<card :bordered="false">
		<p slot="title">通知设置</p>
		<i-form :label-width="200" :model="form" ref="form" :rules="rules">
			<row>
				<i-col span="12">
					<form-item label="系统通知账号" prop="imageProcessingEngine">
						<i-input placeholder="系统通知账号, 和网易对接, 保证网易账号存在" v-model="form.system">
						</i-input>
					</form-item>
				</i-col>
			</row>
			<row>
				<i-col span="12">
					<form-item label="系统通知头像" prop="fileMaxSize">
						<uploader
							v-model="fileList.system"
							:max="1"
							:on-success="systemIconSuccess"
							:on-remove="systemIconRemove"
						></uploader>
					</form-item>
				</i-col>
			</row>

			<row>
				<i-col span="12">
					<form-item label="官方通知账号" prop="imageProcessingEngine">
						<i-input placeholder="系统通知账号, 和网易对接, 保证网易账号存在" v-model="form.notice">
						</i-input>
					</form-item>
				</i-col>
			</row>
			<row>
				<i-col span="12">
					<form-item label="官方通知头像" prop="fileMaxSize">
						<uploader
							v-model="fileList.notice"
							:max="1"
							:on-success="noticeIconSuccess"
							:on-remove="noticeIconRemove"
						></uploader>
					</form-item>
				</i-col>
			</row>
			<row>
				<i-col span="12">
					<form-item label="订单通知账号" prop="imageProcessingEngine">
						<i-input
							placeholder="订单通知账号, 和网易对接, 保证网易账号存在"
							v-model="form.order"
						></i-input>
					</form-item>
				</i-col>
			</row>
			<row>
				<i-col span="12">
					<form-item label="订单通知头像" prop="fileMaxSize">
						<uploader
							v-model="fileList.order"
							:max="1"
							:on-success="orderIconSuccess"
							:on-remove="orderIconRemove"
						></uploader>
					</form-item>
				</i-col>
			</row>
			<row>
				<i-col span="12">
					<form-item>
						<i-button :loading="loading" type="primary" @click.native="submit">
							<span v-if="!loading">确认提交</span>
							<span v-else>正在提交…</span>
						</i-button>
						<i-button @click.native="modal.visible = true">发送测试消息</i-button>
					</form-item>
				</i-col>
			</row>
		</i-form>
		<modal :loading="modal.loading" title="发送测试消息" :value="modal.visible" @on-cancel="modal.visible = false"
		       @on-ok="send">
			<i-form label-position="left" :model="notice" ref="sendForm" :rules="testRules">
				<form-item prop="type">
					<i-select placeholder="推送类型" v-model="notice.type">
						<i-option v-for="(value ,type) in dataSource.types" :value="type" :key="type">{{ value }}</i-option>
					</i-select>
				</form-item>
				<form-item prop="type">
					<i-select placeholder="消息类型" v-model="notice.msg_type">
						<i-option v-for="(value ,type) in dataSource.msgTypes" :value="type" :key="type">{{ value }}</i-option>
					</i-select>
				</form-item>
				<form-item label="接收用户名" prop="passport">
					<i-input placeholder="请输入接收消息的用户名" v-model="notice.passport"></i-input>
				</form-item>
				<form-item label="消息内容" prop="content">
					<i-input placeholder="请输入测试消息内容" v-model="notice.content"></i-input>
				</form-item>
			</i-form>
		</modal>
	</card>
</template>