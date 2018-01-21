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
                query be_role_list ($filters: BeRoleFilter!,$pagination:InputPagination!){
                  be_role_list : be_role_list(filters: $filters,pagination:$pagination){
                    list{
                        id,
                        title,
                        name,
                        type,
                        can_permission
                    }
                  }
                }
                `,
                variables     : {
                    filters    : {
                        type : 'backend'
                    },
                    pagination : {
                        page : 1
                    }
                },
                operationName : 'be_role_list'
            }).then(response => {
                // todo 需要检测空
                const {data} = response.data;
                next(vm => {
                    vm.role_list = data.be_role_list.list;
                    vm.role_pagination = data.be_role_list.pagination;
                    injection.loading.finish();
                });
                injection.$notice.error({
                    title : response.data.errors,
                });
            }).catch(() => {
                injection.loading.error();
            });
        },
        data() {
            const that = this;
            return {
                // 角色列表
                role_list       : [],
                // 分页
                role_pagination : {
                    page : '',
                    size : ''
                },
                // 角色列定义
                role_column     : [
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
                        title : '操作',
                        key   : 'handle',
                        align : 'center',
                        render(h, params) {
                            if (params.row.can_permission) {
                                return h('div', [
                                    h('i-button', {
                                        props : {
                                            type : 'warning',
                                            size : 'small'
                                        },
                                        style : {
                                            marginRight : '5px'
                                        },
                                        on    : {
                                            click : () => {
                                                that.DictionSet(params);
                                            }
                                        }
                                    }, '权限'),
                                    h('i-button', {
                                        props : {
                                            type : 'primary',
                                            size : 'small'
                                        },
                                        style : {
                                            marginRight : '5px'
                                        },
                                        on    : {
                                            click : () => {
                                                that.updateRole(params);
                                            }
                                        }
                                    }, '编辑'),
                                    h('poptip', {
                                        props : {
                                            confirm   : true,
                                            width     : 200,
                                            title     : '确认删除此角色 ? ',
                                            placement : 'left'
                                        },
                                        on    : {
                                            'on-ok' : () => {
                                                that.remove(params);
                                            }
                                        }
                                    }, [
                                        h('i-button', {
                                            props : {
                                                type : 'error',
                                                size : 'small'
                                            },
                                        }, '删除')
                                    ])
                                ]);
                            }
                            return h('div', [
                                h('i-button', {
                                    props : {
                                        type : 'primary',
                                        size : 'small'
                                    },
                                    style : {
                                        marginRight : '5px'
                                    },
                                    on    : {
                                        click : () => {
                                            that.UpdateRole(params);
                                        }
                                    }
                                }, '编辑'),
                                h('poptip', {
                                    props : {
                                        confirm   : true,
                                        width     : 200,
                                        title     : '确认删除此角色 ? ',
                                        placement : 'left'
                                    },
                                    on    : {
                                        'on-ok' : () => {
                                            that.remove(params);
                                        }
                                    }
                                }, [
                                    h('i-button', {
                                        props : {
                                            type : 'error',
                                            size : 'small'
                                        },
                                    }, '删除')
                                ])
                            ]);
                        },
                    },
                ],
                // 编辑/创建
                isEdit          : true,
                isAdd           : false,
                modalCreate     : false,
                // 编辑权限
                modalDiction    : false,
                // 权限设置弹框信息
                diction_data    : [],
                totalCount      : 100,
                items           : [],
                form            : {},
                modal           : false,
                loading         : false,
                rules           : {
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

                diction_columns : [
                    {
                        title : '分组',
                        key   : 'title'
                    },
                    {
                        title : '权限',
                        key   : 'permission'
                    },
                ],
                createModal     : {
                    parent_org : '',
                    title      : '新增角色',
                },
                role_2e         : {
                    title : '新增角色',
                },
                role            : {
                    // id          : '',
                    name        : '',
                    title       : '',
                    guard       : '',
                    description : '',
                },
                // 新增角色类型
                guardList       :
                    [
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
                searchValue     :
                    ''
            };
        },
        methods : {
            // 清空再次点击新增的数据
            reset() {
                // this.role.id = '';
                this.role.name = '';
                this.role.title = '';
                this.role.guard = '';
                this.role.description = '';
            },
            show(index) {
                console.log(index);
                this.$Modal.info({
                    title : 'User Info',
                    // content : `Name：${this.data6[index].name}<br>
                    // Age：${this.data6[index].age}<br>Address：${this.data6[index].address}`
                });
            },
            // 删除数据
            remove(params) {
                const self = this;
                self.loading = true;
                injection.http.post(`${window.api}/g/backend`, {
                    query         : `
                        mutation be_role_do($action: RoleDoAction!, $id: Int) {
                          be_role_do : be_role_do(action: $action, id: $id) {
                            status
                            message
                            data
                          }
                        }`,
                    variables     : {
                        action : 'delete',
                        id     : params.row.id
                    },
                    operationName : 'be_role_do'
                }).then((data) => {
                    console.log(data);
                    self.$notice.open({
                        title : '删除数据成功！',
                    });
                    self.refresh();
                }).catch((error) => {
                    console.log(error);
                    self.$notice.error({
                        title : '删除数据失败！',
                    });
                }).finally(() => {
                    self.loading = false;
                });
            },
            // 点击新增的弹出框
            createOrganization() {
                // 重置弹框数据
                this.reset();
                this.modalCreate = true;
                this.role_2e.title = '增加角色';
                // 隐藏编辑按钮
                this.isEdit = true;
                this.isAdd = false;
            },
            // 刷新页面
            refresh() {
                const self = this;
                self.$loading.start();
                injection.http.post(`${window.api}/g/backend`, {
                    query         : `
                       query be_role_list ($filters: BeRoleFilter!,$pagination:InputPagination!){
                          be_role_list : be_role_list(filters: $filters,pagination:$pagination){
                            list{
                                id,
                                title,
                                name,
                                type,
                                can_permission
                            }
                          }
                        }
                        `,
                    variables     : {
                        filters    : {
                            type : 'backend'
                        },
                        pagination : {
                            page : 1
                        }
                    },
                    operationName : 'be_role_list'
                }).then(response => {
                    const {data} = response.data;
                    self.role_list = data.be_role_list.list;
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
                this.modalCreate = false;
            },
            // 增加角色
            submitCreate() {
                const self = this;
                console.log(3);
                self.loading = true;
                console.log(self.$refs);
                console.log(self.$refs.form);
                self.$refs.createRole.validate(valid => {
                    if (valid) {
                        self.$http.post(`${window.api}/g/backend`, {
                            query         : `
                                mutation be_role_2e($item:InputRole!)
                                {
                                  be_role_2e:be_role_2e(item:$item){
                                    status,
                                    message
                                  }
                                }`,
                            variables     : {
                                item : self.role
                            },
                            operationName : 'be_role_2e'
                        }).then((data) => {
                            console.log(data);
                            self.$notice.open({
                                title : '保存成功！',
                            });
                            // 重置弹窗
                            self.reset();
                            // 刷新页面
                            self.refresh();
                        }).catch((error) => {
                            console.log(error);
                            self.$notice.error({
                                title : '保存成功！',
                            });
                        }).finally(() => {
                            self.loading = false;
                        });
                    }
                    else {
                        self.$notice.error({
                            title : '请正确填写完整的数据',
                        });
                        self.loading = false;
                    }
                });
            },
            // 编辑保存
            submitUpdate(roleId) {
                const self = this;
                // console.log('=======点击保存会传后台的id是下面这个======');
                // console.log(roleId);
                self.loading = true;
                console.log(self.$refs);
                console.log(self.$refs.form);
                self.$refs.createRole.validate(valid => {
                    if (valid) {
                        self.$http.post(`${window.api}/g/backend`, {
                            query         : `
                                mutation be_role_2e($item:InputRole!)
                                {
                                  be_role_2e:be_role_2e(item:$item){
                                    status,
                                    message
                                  }
                                }`,
                            variables     : {
                                item : roleId
                            },
                            operationName : 'be_role_2e'
                        }).then((data) => {
                            console.log(data);
                            self.$notice.open({
                                title : '保存成功！',
                            });
                            // 重置弹窗
                            self.reset();
                            // 刷新页面
                            self.refresh();
                        }).catch((error) => {
                            console.log(error);
                            self.$notice.error({
                                title : '保存成功！',
                            });
                        }).finally(() => {
                            self.loading = false;
                        });
                    }
                    else {
                        self.$notice.error({
                            title : '请正确填写完整的数据',
                        });
                        self.loading = false;
                    }
                });
            },
            // 编辑角色
            updateRole(params) {
                console.log(params.row);
                // 显示隐藏按钮
                this.isEdit = false;
                this.isAdd = true;
                this.modalCreate = true;
                this.role_2e.title = '编辑角色';
                this.role.id = params.row.id;
                this.role.name = params.row.name;
                this.role.title = params.row.title;
                this.role.guard = params.row.type;
            },
            // 权限设置
            DictionSet(params) {
                this.modalDiction = true;
                const self = this;
                self.loading = true;
                self.$http.get(`${window.api}/system/permission?role_id=${params.row.id}`)
                    .then((resp) => {
                        // let data = [];
                        console.log(resp.data.data);
                        // todo 需要循环成 tabpane 支持的格式
                        resp.data.data.forEach((item, key) => {
                            console.log(item, key);
                        });
                        self.diction_data = resp.data.data;
                    })
                    .catch((error) => {
                        console.log(error);
                    })
                    .finally(() => {
                        self.loading = false;
                    });
            },
            // 查找
            search() {
                //     console.log('Search');
                //
                //     const self = this;
                //     console.log(self.searchValue);
                //     self.loading = true;
                //
                //     self.$refs.http.post(`${window.api}/g/backend`, {
                //         query         : `
                //     query roles ($filters:RoleFilter!) {
                //         roles : roles(filters: $filters){
                //             id,
                //             title,
                //             name,
                //             type
                //         }
                //     }`,
                //         variables     : {
                //             filters : {
                //                 type : 'backend'
                //             }
                //         },
                //         operationName : 'roles'
                //     }).then(response => {
                //         const {data} = response.data;
                //         // console.log(pagination);
                //             data.roles.forEach(item => {
                //                 console.log(item);
                //             });
                //         self.roles_data = data.roles;
                //         console.log('=========roles==========');
                //         console.log(data.roles);
                //     }).catch(() => {
                //         injection.loading.error();
                //     });
            },
            // 分页组件
            // changePage() {
            //     console.log('分页组件');
            // 当前table数据源更换掉
            // this.roles_data = xxx;
            // }
            changePageSize(size) {
                const self = this;
                self.pageSize = size;
                self.changePage();
            }
        },
        mounted() {
            this.$store.commit('title', trans('角色管理role'));
        },
    };

</script>
<template>
    <div class="page-wrap">
        <div class="return-link-title tab-pane-title"
             style="color: #328cf1;background-color: #ffffff;font-size: 23px;padding:15px;margin-bottom: 10px;">
            <span>角色管理</span>
        </div>
        <div style="background-color: #ffffff">
            <i-button class="btn-action" type="success"
                      @click.native="createOrganization" style="margin-bottom:8px;margin-top: 8px;margin-left: 8px">╋新增
            </i-button>
            <i-input :rows="2" style="width: 200px;float: right;margin-bottom:8px;margin-top: 8px"
                     placeholder="搜索..." icon="ios-search" on-click="search()" v-model="searchValue"></i-input>
            <i-table :columns="role_column" :data="role_list"></i-table>
        </div>
        <!--分页组件-->
        <div style="margin: 10px;overflow: hidden">
            <div style="float: right;">
                <page :total="totalCount" :current="page" @on-change="changePage" @page-size="pageSize" name="ws" show-elevator="Boolean"></page>
            </div>
        </div>
        <!--权限弹出框-->
        <modal v-model="modalDiction"
               title="权限设置"
               class="setting-modal-delete setting-modal-action">
            <tabs type="card">
                <tab-pane v-for="diction in diction_data" :key="diction.key" :label="diction.title">
                    <i-table :columns="diction_columns" :data="diction.groups"></i-table>
                </tab-pane>
            </tabs>
            <i-button class="btn-action" type="success"
                      @click.native="">保存
            </i-button>
        </modal>
        <!--新增弹出框start-->
        <modal v-model="modalCreate"
               :title="role_2e.title"
               class="setting-modal-delete setting-modal-action">
            <div>
                <i-form ref="createRole" :model="role" :rules="rules" :label-width="110">
                    <row>
                        <i-col span="14">
                            <form-item label="角色标识(英文)" prop="name">
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
                    <!--隐藏域记录编辑时候的id-->
                    <row>
                        <i-col span="14">
                            <form-item label="id" prop="title">
                                <!--style="display:none;"-->
                                <i-input v-model="role.id"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="14">
                            <form-item v-show="isAdd">
                                <i-button :loading="loading" @click.native="submitCreate"
                                          class="btn-group" type="success">
                                    <span v-if="!loading">确认提交(编辑)</span>
                                    <span v-else>正在提交…</span>
                                </i-button>
                            </form-item>
                            <form-item v-show="isEdit">
                                <i-button :loading="loading" @click.native="submitUpdate(role.id)"
                                          class="btn-group" type="success">
                                    <span v-if="!loading">确认提交(增加)</span>
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