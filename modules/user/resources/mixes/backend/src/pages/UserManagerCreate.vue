<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            if (to.query.type === '1') {
                next(vm => {
                    vm.parent.type = to.query.type;
                    vm.parent.name = to.query.name;
                });
            } else if (to.query.type === '0') {
                next(vm => {
                    vm.parent.type = to.query.type;
                });
            }
        },
        data() {
            return {
                action: `${window.api}/zn/admin/upload`,
                form: {
                    image: '',
                    name: '',
                    sex: '1',
                },
                loading: false,
                parent: {
                    name: '',
                    type: '',
                },
                rules: {
                    email: [
                        {
                            message: '邮箱格式不正确',
                            trigger: 'blur',
                            type: 'email',
                        },
                    ],
                },
            };
        },
        methods: {
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
            removeLogo() {
                this.form.image = '';
            },
            submit() {},
            uploadBefore() {
                injection.loading.start();
            },
            uploadExceeded(file) {
                const self = this;
                if (file.size > 4096) {
                    self.$notice.error({
                        title: '该文件大小超出2M,请重新选择',
                    });
                }
            },
            uploadError(error, data) {
                const self = this;
                injection.loading.error();
                if (typeof data.message === 'object') {
                    for (const p in data.message) {
                        self.$notice.error({
                            title: data.message[p],
                        });
                    }
                } else {
                    self.$notice.error({
                        title: data.message,
                    });
                }
            },
            uploadFormatError(file) {
                this.$notice.warning({
                    title: '文件格式不正确',
                    desc: `文件 ${file.name} 格式不正确`,
                });
            },
            uploadSuccess(data) {
                const self = this;
                injection.loading.finish();
                self.$notice.open({
                    title: data.message,
                });
                self.form.image = data.data.image;
            },
        },
    };
</script>
<template>
    <div class="member-warp">
        <div class="user-manager-create">
            <div class="return-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span v-if="parent.type === '0'">用户管理-新增</span>
                <span v-if="parent.type === '1'">用户管理-编辑"{{ parent.name }}"</span>
            </div>
            <card :bordered="false">
                <i-form ref="form" :model="form" :rules="rules" :label-width="200">
                    <h5>基本资料</h5>
                    <row>
                        <i-col span="12">
                            <form-item label="用户名" prop="name">
                                <i-input v-model="form.name"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="密码" prop="password">
                                <i-input type="password" v-model="form.password"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="24">
                            <form-item label="头像" prop="image">
                                <div class="image-preview" v-if="form.image">
                                    <img :src="form.image">
                                    <icon type="close" @click.native="removeLogo"></icon>
                                </div>
                                <upload :action="action"
                                        :before-upload="uploadBefore"
                                        :format="['jpg','jpeg','png']"
                                        :headers="{
                                            Authorization: `Bearer ${$store.state.token.access_token}`
                                        }"
                                        :max-size="4096"
                                        :on-exceeded-size="uploadExceeded"
                                        :on-error="uploadError"
                                        :on-format-error="uploadFormatError"
                                        :on-success="uploadSuccess"
                                        ref="upload"
                                        :show-upload-list="false"
                                        v-if="form.image === '' || form.image === null">
                                </upload>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="邮箱" prop="email">
                                <i-input v-model="form.email"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="状态" prop="status">
                                <i-switch size="large" v-model="form.status">
                                    <span slot="open">开启</span>
                                    <span slot="close">关闭</span>
                                </i-switch>
                            </form-item>
                        </i-col>
                    </row>
                    <h5>详细资料</h5>
                    <row>
                        <i-col span="12">
                            <form-item label="用户昵称" prop="nike_name">
                                <i-input v-model="form.nike_name"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="真实姓名" prop="real_name">
                                <i-input v-model="form.real_name"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="性别" prop="sex">
                                <radio-group v-model="form.sex">
                                    <radio label="1">男</radio>
                                    <radio label="0">女</radio>
                                </radio-group>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="生日" prop="date">
                                <date-picker type="date" placeholder="选择日期"
                                             v-model="form.date"></date-picker>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="所属角色" prop="role">
                                <i-input :disabled="parent.type === '1'"
                                         v-model="form.role"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="所属部门" prop="department">
                                <i-input :disabled="parent.type === '1'"
                                         v-model="form.department"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item>
                                <i-button :loading="loading" @click.native="submit"
                                          class="btn-group" type="primary">
                                    <span v-if="!loading">确认提交</span>
                                    <span v-else>正在提交…</span>
                                </i-button>
                            </form-item>
                        </i-col>
                    </row>
                </i-form>
            </card>
        </div>
    </div>
</template>
