<script>
import injection, {trans} from '../helpers/injection';

const imQuery = `
query {
	imSystem:be_setting(key:"system::im.system_account"){
		key,value
	},
	imNotice:be_setting(key:"system::im.notice_account"){
		key,value
	},
	imOrder:be_setting(key:"system::im.order_account"){
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
				imSystem,
				imNotice,
				imOrder
			} = response.data.data;
			next(vm => {
				vm.form.system = imSystem;
				vm.form.notice = imNotice;
				vm.form.order = imOrder;
				injection.loading.finish();
			});
		}).catch(() => {
			injection.loading.error();
		});
	},
	data() {
		return {
			form    : {
				system : '',
				notice : '',
				order  : '',
			},
			loading : false,
			rules   : {
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
		};
	},
	methods : {
		submit() {
			const self = this;
			self.loading = true;
			self.$refs.form.validate(valid => {
				if (valid) {
					self.$http.post(`${window.api}/administration`, {
						query : `mutation {
                                canManagementFileExtension: setting(
                                    key:"attachment.manager.file",
                                    value:"${self.form.canManagementFileExtension}"
                                ),
                                canManagementImageExtension: setting(
                                    key:"attachment.manager.image",
                                    value:"${self.form.canManagementImageExtension}"
                                ),
                                canUploadCatcherExtension: setting(
                                    key:"attachment.format.catcher",
                                    value:"${self.form.canUploadCatcherExtension}"
                                ),
                                canUploadFileExtension: setting(
                                    key:"attachment.format.file",
                                    value:"${self.form.canUploadFileExtension}"
                                ),
                                canUploadImageExtension: setting(
                                    key:"attachment.format.image",
                                    value:"${self.form.canUploadImageExtension}"
                                ),
                                canUploadVideoExtension: setting(
                                    key:"attachment.format.video",
                                    value:"${self.form.canUploadVideoExtension}"
                                ),
                                fileMaxSize: setting(
                                    key:"attachment.limit.file",
                                    value:"${self.form.fileMaxSize}"
                                ),
                                imageMaxSize: setting(
                                    key:"attachment.limit.image",
                                    value:"${self.form.imageMaxSize}"
                                ),
                                imageProcessingEngine: setting(
                                    key:"attachment.engine",
                                    value:"${self.form.imageProcessingEngine}"
                                ),
                                videoMaxSize: setting(
                                    key:"attachment.limit.video",
                                    value:"${self.form.videoMaxSize}"
                                ),
                            }`,
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
	},
	mounted() {
		this.$store.commit('title', trans('administration.title.upload'));
	},
};
</script>
<template>
	<card :bordered="false">
		<p slot="title">上传配置</p>
		<i-form :label-width="200" :model="form" ref="form" :rules="rules">
			<row>
				<i-col span="12">
					<form-item label="系统通知账号" prop="imageProcessingEngine">
						<i-input placeholder="系统通知账号, 和网易对接, 保证网易账号存在" v-model="form.system.value">
						</i-input>
					</form-item>
				</i-col>
			</row>
			<row>
				<i-col span="12">
					<form-item label="系统通知头像" prop="fileMaxSize">
						<upload action="//jsonplaceholder.typicode.com/posts/">
							<i-button type="ghost" icon="ios-cloud-upload-outline">上传头像</i-button>
						</upload>
					</form-item>
				</i-col>
			</row>

			<row>
				<i-col span="12">
					<form-item label="官方通知账号" prop="imageProcessingEngine">
						<i-input placeholder="系统通知账号, 和网易对接, 保证网易账号存在" v-model="form.notice.value">
						</i-input>
					</form-item>
				</i-col>
			</row>
			<row>
				<i-col span="12">
					<form-item label="官方通知头像" prop="fileMaxSize">
						<upload action="//jsonplaceholder.typicode.com/posts/">
							<i-button type="ghost" icon="ios-cloud-upload-outline">上传头像</i-button>
						</upload>
					</form-item>
				</i-col>
			</row>
			<row>
				<i-col span="12">
					<form-item label="订单通知账号" prop="imageProcessingEngine">
						<i-input placeholder="订单通知账号, 和网易对接, 保证网易账号存在" v-model="form.order.value">
							<span slot="append">KB</span>
						</i-input>
					</form-item>
				</i-col>
			</row>
			<row>
				<i-col span="12">
					<form-item label="订单通知头像" prop="fileMaxSize">
						<upload action="//jsonplaceholder.typicode.com/posts/">
							<i-button type="ghost" icon="ios-cloud-upload-outline">上传头像</i-button>
						</upload>
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
					</form-item>
				</i-col>
			</row>
		</i-form>
	</card>
</template>