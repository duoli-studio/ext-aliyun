<script>
    import {trans} from '../helpers/injection';

    export default {
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

                columns1 : [
                    {
                        title  : 'ID',
                        key    : 'id',
                        render : (h, params) => h('div', [
                            h('Icon', {
                                props : {
                                    type : 'person'
                                }
                            }),
                            h('strong', params.row.name)
                        ])
                    },
                    {
                        title : '用户名',
                        key   : 'userName'
                    },
                    {
                        title : '手机号',
                        key   : 'phone'
                    },
                    // {
                    //     title : '操作',
                    //     key   : 'action',
                    //     width : 150,
                    //     align : 'center',
                    //    render  : (h, params) => {
                    //        return h('div', [
                    //            h('Button', {
                    //                props   : {
                    //                    type    : 'primary',
                    //                    size    : 'small'
                    //                },
                    //                style   : {
                    //                    marginRight : '5px'
                    //                },
                    //                on  : {
                    //                    click   : () => {
                    //                        this.show(params.index);
                    //                    }
                    //                }
                    //            }, 'View'),
                    //            h('Button', {
                    //                props   : {
                    //                    type    : 'error',
                    //                    size    : 'small'
                    //                },
                    //                on  : {
                    //                    click   : () => {
                    //                        this.remove(params.index);
                    //                    }
                    //                }
                    //            }, 'Delete')
                    //        ]);
                    //    }
                    // }
                ],
                data1    : [
                    {
                        id       : 1,
                        userName : 'John Brown',
                        phone    : 18
                    },
                    {
                        id       : 2,
                        userName : 'Jim Green',
                        phone    : 24
                    },
                    {
                        id       : 3,
                        userName : 'Joe Black',
                        phone    : 30
                    },
                    {
                        id       : 4,
                        userName : 'Jon Snow',
                        phone    : 26
                    }
                ]
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
                                    key:"attachment.limit.image",                                   value:"${self.form.imageMaxSize}"
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

    <div>
        <Icon type="checkmark" />
        <Form inline>
            <FormItem prop="user">
                <Input type="text" placeholder="Username">
                <Icon type="ios-person-outline"></Icon>
                </Input>
            </FormItem>
            <FormItem prop="password">
                <Input type="password" placeholder="Password">
                <Icon type="ios-locked-outline"></Icon>
                </Input>
            </FormItem>
            <FormItem>
                <Button type="primary">Signin</Button>
            </FormItem>
        </Form>
        <i-table height="200" :columns="columns1" :data="data1"></i-table>
    </div>
</template>