<script>
	import injection, {trans} from '../helpers/injection';

	export default {
		beforeRouteEnter(to, from, next) {
			injection.loading.start();
			injection.loading.start();
			injection.http.post(`${window.api}/administration`, {
				query : 'query {' +
				'canManagementFileExtension:setting(key:"attachment.manager.file"),' +
				'canManagementImageExtension:setting(key:"attachment.manager.image"),' +
				'canUploadCatcherExtension:setting(key:"attachment.format.catcher"),' +
				'canUploadFileExtension:setting(key:"attachment.format.file"),' +
				'canUploadImageExtension:setting(key:"attachment.format.image"),' +
				'canUploadVideoExtension:setting(key:"attachment.format.video"),' +
				'fileMaxSize:setting(key:"attachment.limit.file"),' +
				'imageMaxSize:setting(key:"attachment.limit.image"),' +
				'imageProcessingEngine:setting(key:"attachment.engine"),' +
				'videoMaxSize:setting(key:"attachment.limit.video"),' +
				'}',
			}).then(response => {
				const {
					      canManagementFileExtension,
					      canManagementImageExtension,
					      canUploadCatcherExtension,
					      canUploadFileExtension,
					      canUploadImageExtension,
					      canUploadVideoExtension,
					      fileMaxSize,
					      imageMaxSize,
					      imageProcessingEngine,
					      videoMaxSize,
				      } = response.data.data;
				next(vm => {
					vm.form.canManagementFileExtension = canManagementFileExtension.join(',');
					vm.form.canManagementImageExtension = canManagementImageExtension.join(',');
					vm.form.canUploadCatcherExtension = canUploadCatcherExtension.join(',');
					vm.form.canUploadFileExtension = canUploadFileExtension.join(',');
					vm.form.canUploadImageExtension = canUploadImageExtension.join(',');
					vm.form.canUploadVideoExtension = canUploadVideoExtension.join(',');
					vm.form.fileMaxSize = parseInt(fileMaxSize.join(''), 10);
					vm.form.imageMaxSize = parseInt(imageMaxSize.join(''), 10);
					vm.form.imageProcessingEngine = imageProcessingEngine.join('');
					vm.form.videoMaxSize = parseInt(videoMaxSize.join(''), 10);
					injection.loading.finish();
				});
			}).catch(() => {
				injection.loading.error();
			});
		},
		data() {
			return {
				form    : {
					canManagementFileExtension  : '',
					canManagementImageExtension : '',
					canUploadCatcherExtension   : '',
					canUploadFileExtension      : '',
					canUploadImageExtension     : '',
					canUploadVideoExtension     : '',
					fileMaxSize                 : 0,
					imageMaxSize                : 0,
					imageProcessingEngine       : 'gd',
					videoMaxSize                : 0,
				},
				loading : false,
				rules   : {
					canManagementFileExtension  : [
						{
							required : true,
							type     : 'string',
							message  : '请输入允许管理文件的扩展名',
							trigger  : 'change',
						},
					],
					canManagementImageExtension : [
						{
							required : true,
							type     : 'string',
							message  : '请输入允许管理图片的扩展名',
							trigger  : 'change',
						},
					],
					canUploadCatcherExtension   : [
						{
							required : true,
							type     : 'string',
							message  : '请输入允许管理截图的扩展名',
							trigger  : 'change',
						},
					],
					canUploadFileExtension      : [
						{
							required : true,
							type     : 'string',
							message  : '请输入允许上传文件的扩展名',
							trigger  : 'change',
						},
					],
					canUploadImageExtension     : [
						{
							required : true,
							type     : 'string',
							message  : '请输入允许管理图片的扩展名',
							trigger  : 'change',
						},
					],
					canUploadVideoExtension     : [
						{
							required : true,
							type     : 'string',
							message  : '请输入允许上传视频的扩展名',
							trigger  : 'change',
						},
					],
					fileMaxSize                 : [
						{
							required : true,
							type     : 'integer',
							message  : '请输入附件大小',
							trigger  : 'change',
						},
					],
					imageMaxSize                : [
						{
							required : true,
							type     : 'integer',
							message  : '请输入图片大小',
							trigger  : 'change',
						},
					],
					videoMaxSize                : [
						{
							required : true,
							type     : 'integer',
							message  : '请输入视频大小',
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
                    <form-item label="图片处理引擎" prop="imageProcessingEngine">
                        <radio-group v-model="form.imageProcessingEngine">
                            <radio label="gd">GD 库</radio>
                        </radio-group>
                    </form-item>
                </i-col>
            </row>
            <row>
                <i-col span="12">
                    <form-item label="附件大小" prop="fileMaxSize">
                        <i-input :number="true" placeholder="请输入附件大小" v-model="form.fileMaxSize">
                            <span slot="append">KB</span>
                        </i-input>
                    </form-item>
                </i-col>
            </row>
            <row>
                <i-col span="12">
                    <form-item label="图片大小" prop="imageMaxSize">
                        <i-input :number="true" placeholder="请输入图片大小" v-model="form.imageMaxSize">
                            <span slot="append">KB</span>
                        </i-input>
                    </form-item>
                </i-col>
            </row>
            <row>
                <i-col span="12">
                    <form-item label="视频大小" prop="videoMaxSize">
                        <i-input :number="true" placeholder="请输入视频大小" v-model="form.videoMaxSize">
                            <span slot="append">KB</span>
                        </i-input>
                    </form-item>
                </i-col>
            </row>
            <row>
                <i-col span="12">
                    <form-item label="允许图片的扩展名" prop="canUploadImageExtension">
                        <i-input type="textarea" placeholder="请输入允许管理图片的扩展名" v-model="form.canUploadImageExtension"
                                 :autosize="{minRows: 2,maxRows: 5}"></i-input>
                    </form-item>
                </i-col>
            </row>
            <row>

                <i-col span="12">
                    <form-item label="允许上传截图的扩展名" prop="canUploadCatcherExtension">
                        <i-input type="textarea" placeholder="请输入允许管理截图的扩展名" v-model="form.canUploadCatcherExtension"
                                 :autosize="{minRows: 2,maxRows: 5}"></i-input>
                    </form-item>
                </i-col>
            </row>
            <row>
                <i-col span="12">
                    <form-item label="允许上传视频的扩展名" prop="canUploadVideoExtension">
                        <i-input type="textarea" placeholder="请输入允许上传视频的扩展名" v-model="form.canUploadVideoExtension"
                                 :autosize="{minRows: 2,maxRows: 5}"></i-input>
                    </form-item>
                </i-col>
            </row>
            <row>
                <i-col span="12">
                    <form-item label="允许上传文件的扩展名" prop="canUploadFileExtension">
                        <i-input type="textarea" placeholder="请输入允许上传文件的扩展名" v-model="form.canUploadFileExtension"
                                 :autosize="{minRows: 2,maxRows: 5}"></i-input>
                    </form-item>
                </i-col>
            </row>
            <row>
                <i-col span="12">
                    <form-item label="允许管理图片的扩展名" prop="canManagementImageExtension">
                        <i-input type="textarea" placeholder="请输入允许管理图片的扩展名" v-model="form.canManagementImageExtension"
                                 :autosize="{minRows: 2,maxRows: 5}"></i-input>
                    </form-item>
                </i-col>
            </row>
            <row>
                <i-col span="12">
                    <form-item label="允许管理文件的扩展名" prop="canManagementFileExtension">
                        <i-input type="textarea" placeholder="请输入允许管理文件的扩展名" v-model="form.canManagementFileExtension"
                                 :autosize="{minRows: 2,maxRows: 5}"></i-input>
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