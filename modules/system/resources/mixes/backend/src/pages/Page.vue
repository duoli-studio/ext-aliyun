<script>
	import injection from '../helpers/injection';

	export default {
		beforeRouteEnter(to, from, next) {
			injection.loading.start();
			let path = to.path.replace('/', '-');
			if (path.indexOf('-') === 0) {
				path = path.substring(1, path.length);
			}
			injection.http.get(`${window.api}backend/system/layout/page/${path}`).then(response => {
				const {initialization, tabs} = response.data.data;
				next(vm => {
					Object.keys(tabs).forEach(index => {
						tabs[index].loading = false;
					});
					vm.initialization = initialization;
					vm.path = path;
					vm.tabs = tabs;
					injection.loading.finish();
				});
			}).catch(() => {
				injection.loading.error();
				injection.notice.error({
					title : '初始化页面失败！',
				});
			});
		},
		data() {
			return {
				initialization : {},
				path           : '',
				tabs           : {},
				type           : '',
			};
		},
		methods : {
			refresh() {
				const self = this;
				self.$notice.open({
					title : '正在刷新数据...',
				});
				self.$loading.start();
				self.$http.get(`${window.api}backend/system/layout/page/${self.path}`).then(response => {
					const {initialization, tabs} = response.data.data;
					Object.keys(tabs).forEach(index => {
						tabs[index].loading = false;
					});
					self.initialization = initialization;
					self.tabs = tabs;
					self.$loading.finish();
					self.$notice.open({
						title : '刷新数据成功！',
					});
				}).catch(() => {
					self.$loading.error();
					self.$notice.error({
						title : '刷新数据失败！',
					});
				});
			},
			submit(identification) {
				const self = this;
				const tab = self.tabs[identification];
				tab.loading = true;
				self.$refs[identification][0].validate(valid => {
					if (valid) {
						if (tab.submit) {
							self.$http.post(tab.submit, tab.fields).then(response => {
								self.$notice.open({
									title : response.data.message,
								});
								self.refresh();
							}).catch(() => {
								self.$notice.error({
									title : '提交失败！',
								});
							}).finally(() => {
								tab.loading = false;
							});
						}
						else {
							tab.loading = false;
							self.$notice.error({
								title : '配置错误',
							});
						}
					}
					else {
						self.$notice.error({
							title : '请正确填写设置信息！',
						});
						tab.loading = false;
					}
				});
			},
		},
	};
</script>
<template>
    <div class="page-wrap">
        <tabs>
            <tab-pane :key="identification" :label="tab.title" :name="identification"
                      v-for="(tab, identification) in tabs">
                <card :bordered="false">
                    <i-form :label-width="200" :model="tab.fields" :ref="identification">
                        <row :key="key" v-for="(field, key) in tab.fields">
                            <i-col span="12">
                                <form-item :label="field.label" :key="key" :prop="key + '.value'"
                                           :rules="field.validates">
                                    {{ field.rules }}
                                    <i-input v-if="field.type === 'input'" v-model="field.value"></i-input>
                                    <i-input :rows="4" type="textarea" v-if="field.type === 'textarea'"
                                             v-model="field.value"></i-input>
                                    <i-switch v-model="field.value" size="large" v-if="field.type === 'switch'">
                                        <span slot="open">开启</span>
                                        <span slot="close">关闭</span>
                                    </i-switch>
                                    <date-picker :type="field.type"
                                                 v-if="field.type === 'date' ||
                                                       field.type === 'daterange' ||
                                                       field.type === 'datetime'"
                                                 v-model="field.value"></date-picker>
                                    <radio-group v-model="field.value" size="large" v-if="field.type === 'radio'">
                                        <radio :key="i" :value="i" :label="i" v-for="(option, i) in field.opinions">
											<span>{{option}}</span>
										</radio>
                                    </radio-group>
                                    <div class="ivu-upload-wrapper" v-if="field.type === 'picture'">
                                        <div class="preview" v-if="field.value">
                                            <img :src="field.value">
                                            <icon type="close" @click.native="remove(field.id)"></icon>
                                        </div>
                                        <upload :action="action"
                                                :data="{
                                                    id: field.id,
                                                    type: 'information'
                                                }"
                                                :format="['jpg','jpeg','png']"
                                                :headers="{
                                                    Authorization: `Bearer ${$store.state.token.access_token}`
                                                }"
                                                :max-size="2048"
                                                :on-error="uploadError"
                                                :on-format-error="uploadFormatError"
                                                :on-success="uploadSuccess"
                                                ref="upload"
                                                :show-upload-list="false"
                                                v-if="field.value === '' || field.value === null">
                                        </upload>
                                    </div>
                                    <p style="color: #666;">{{ field.description}}</p>
                                </form-item>
                            </i-col>
                        </row>
                        <row>
                            <i-col span="12">
                                <form-item>
                                    <i-button :loading="tab.loading"
                                              type="primary"
                                              @click.native="submit(identification)">
                                        <span v-if="!tab.loading">确认提交</span>
                                        <span v-else>正在提交…</span>
                                    </i-button>
                                </form-item>
                            </i-col>
                        </row>
                    </i-form>
                </card>
            </tab-pane>
        </tabs>
    </div>
</template>