<script>
    import injection, {trans} from '../helpers/injection';
    // import ISelect from "iview/src/components/select/select";

    export default {
        // components : {ISelect},
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
                query roles ($filters:RoleFilter!) {
                    roles : roles(filters: $filters){
                        id,
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
                items       : [],
                value4      : '',
                form        : {
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
                modal       : false,
                modalCreate : false,
                loading     : false,
                rules       : {
                    name        : [
                        {
                            message  : '角色标识不能为空',
                            required : true,
                            trigger  : 'blur',
                        },
                    ],
                    title       : [
                        {
                            message  : '角色名称不能为空',
                            required : true,
                            trigger  : 'blur',
                        },
                    ],
                    guard       : [
                        {
                            message  : '角色类型不能为空',
                            required : true,
                            trigger  : 'blur',
                        },
                    ],
                    description : [
                        {
                            trigger : 'blur',
                        },
                    ],
                },

                roles_column : [
                    {
                        title : '角色ID',
                        key   : 'id'
                    },
                    {
                        title : '角色标识(英文字母)',
                        key   : 'name'
                    },
                    {
                        title : '角色标题',
                        key   : 'title'
                    },
                    {
                        title  : '操作',
                        key    : 'handle',
                        width  : 150,
                        align  : 'center',
                        render(h, params) {
                            return h('div', [
                                h('i-button', {
                                    props : {
                                        type : 'primary',
                                        size : 'small'
                                    },
                                    on    : {
                                        click : () => {
                                            this.show(params.index);
                                        }
                                    }
                                }, '编辑'),
                                h('i-button', {
                                    props : {
                                        type : 'error',
                                        size : 'small'
                                    },
                                    on    : {
                                        click : () => {
                                            this.remove(params.index);
                                        }
                                    }
                                }, '删除')
                            ]);
                        },
                    },
                ],
                createModal  : {
                    parent_org : '',
                    title      : '新增角色',
                },
                role         : {
                    name        : '',
                    title       : '',
                    guard       : '',
                    description : '',
                },
                // 显示列表信息
                roles_data   : [
                    {},
                ],
                // 新增角色类型
                guardList    : [
                    {
                        value : 'backend',
                        label : '后台',
                    },
                    {
                        value : 'web',
                        label : '用户',
                    },
                    {
                        value : 'develop',
                        label : '开发者',
                    },
                ],
            };
        },
        methods : {
            show(index) {
                this.$Modal.info({
                    title   : 'User Info',
                    content : `Name：${this.data1[index].name}<br>Age：${this.data1[index].age}<br>Address：${this.data1[index].address}`
                });
            },
            remove(index) {
                this.data1.splice(index, 1);
            },
            // 点击新增的弹出框
            createOrganization() {
                this.modalCreate = true;
            },
            // 刷新页面
            refresh() {
                const self = this;
                self.$loading.start();
                injection.http.post(`${window.api}/g/backend`, {
                    query         : `
                        query roles ($filters:RoleFilter!) {
                            roles : roles(filters: $filters){
                                id,
                                title,
                                name,
                            }
                        }`,
                    variables     : {
                        filters : {
                            type : 'backend'
                        }
                    },
                    operationName : 'roles'
                }).then(response => {
                    const {data} = response.data;
                    data.roles.forEach(item => {
                        console.log(item);
                    });
                    self.roles_data = data.roles;
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
            },
            // 增加
            submitCreate() {
                const self = this;
                self.loading = true;
                console.log(self.$refs);
                console.log(self.$refs.form);
                self.$refs.createRole.validate(valid => {
                    if (valid) {
                        self.$http.post(`${window.api}/g/backend`, {
                            query         : `
                                mutation role_2e($item:InputRole!)
                                {
                                  role_2e:role_2e(item:$item){
                                    status,
                                    message
                                  }
                                }`,
                            variables     : {
                                item : self.role
                            },
                            operationName : 'role_2e'
                        }).then((data) => {
                            console.log(data);
                            console.log('3');
                            self.$notice.open({
                                title : '数据添加成功！',
                            });
                            self.refresh();
                        }).catch((error) => {
                            console.log(error);
                            self.$notice.error({
                                title : '数据添加失败！',
                            });
                        }).finally(() => {
                            self.loading = false;
                        });
                    }
                    else {
                        self.$notice.error({
                            title : '请正确填写完整的数据',
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
    <div class="page-wrap">
        <i-button class="btn-action" type="ghost"
                  @click.native="createOrganization">╋新增
        </i-button>
        <i-form :label-width="200" inline>
            <i-input :rows="2" style="width: 200px" placeholder="搜索..." icon="ios-search"></i-input>
        </i-form>
        <i-table :columns="roles_column" :data="roles_data"></i-table>

        <modal v-model="modalCreate"
               :title="createModal.title"
               class="setting-modal-delete setting-modal-action">
            <div>
                <i-form ref="createRole" :model="role" :rules="rules" :label-width="110">
                    <row>
                        <i-col span="14">
                            <form-item label="角色标识" prop="name">
                                <i-input v-model="role.name"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="14">
                            <form-item label="角色名称" prop="title">
                                <i-input v-model="role.title"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <form-item label="角色类型" prop="guard">
                            <i-select v-model="role.guard" style="width:300px">
                                <i-option v-for="item in guardList" :value="item.value" :label="item.label"
                                          :key="item.value"></i-option>
                            </i-select>
                        </form-item>
                    </row>
                    <row>
                        <i-col span="14">
                            <form-item label="角色描述" prop="description">
                                <i-input v-model="role.description"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="14">
                            <form-item>
                                <i-button :loading="loading" @click.native="submitCreate"
                                          class="btn-group" type="primary">
                                    <span v-if="!loading">确认提交</span>
                                    <span v-else>正在提交…</span>
                                </i-button>
                            </form-item>
                        </i-col>
                    </row>
                </i-form>
            </div>
        </modal>
    </div>
</template>