<script>
    import injection, {trans} from '../helpers/injection';

    export default {
        /**
         *
         * @param to     即将要进入的目标 路由对象
         * @param from   当前导航正要离开的路由
         * @param next   一定要调用该方法来 resolve 这个钩子。执行效果依赖 next 方法的调用参数
         */
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/g/backend`, {
                query         : `
                query roles ($filters:role_filter!) {
                    roles : roles(filters: $filters){
                        title,
                        name,
                        type
                    }
                }`,
                variables     : {
                    filters : {
                        type : 'backend'
                    }
                },
                operationName : 'roles'
            }).then(response => {
                //
                const {data, pagination} = response.data;

                console.log(pagination);
                next(vm => {
                    data.roles.forEach(item => {
                        console.log(item);
                    });
                    vm.roles_data = data.roles;
                    injection.loading.finish();
                });
            }).catch(() => {
                injection.loading.error();
            });
        },
        data() {
            return {
                value4  : '',
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

                roles_column : [
                    {
                        title : '角色标识(英文字母)',
                        key   : 'name'
                    },
                    {
                        title : '角色标题',
                        key   : 'title'
                    }
                ],
                roles_data   : [
                    {
                        id    : '3',
                        name  : 'name',
                        title : '管理员'
                    },
                ],

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
            show(index) {
                this.$Modal.info({
                    title   : 'User Info',
                    content : `Name：${this.data1[index].name}<br>Age：${this.data1[index].age}<br>Address：${this.data1[index].address}`
                });
            },
            remove(index) {
                this.data1.splice(index, 1);
            }
        },
        mounted() {
            this.$store.commit('title', trans('administration.title.upload'));
        },
    };
</script>
<template>
    <div class="page-wrap">
        <i-form :label-width="200" inline>
            <i-input :rows="2" placeholder="搜索..." icon="ios-search"></i-input>
        </i-form>
        <i-table height="200" :columns="roles_column" :data="roles_data"></i-table>
    </div>
</template>