<script>
    import VueScrollbar from 'vue2-scrollbar';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        components: {
            VueScrollbar,
        },
        data() {
            const reg = /^[0-9]*$/;
            const validatorAuthority = (rule, value, callback) => {
                if (!reg.test(value)) {
                    callback(new Error('权限值为数字'));
                } else {
                    callback();
                }
            };
            return {
                columns: [
                    {
                        align: 'center',
                        key: 'id',
                        title: 'ID',
                    },
                    {
                        align: 'center',
                        key: 'name',
                        title: '角色名称',
                    },
                    {
                        align: 'center',
                        key: 'authority',
                        title: '权限值',
                    },
                ],
                createModal: {
                    authority: null,
                    name: '',
                    title: '',
                },
                deleteModal: {
                    authority: null,
                    id: '',
                    name: '',
                },
                departmentList: [
                    {
                        expand: true,
                        title: '功能1',
                        children: [
                            {
                                expand: false,
                                title: '功能 1-1',
                                children: [
                                    {
                                        expand: false,
                                        title: '功能 1-1-1',
                                        children: [
                                            {
                                                title: '功能 1-1-1-1',
                                            },
                                            {
                                                title: '功能 1-1-1-2',
                                            },
                                        ],
                                    },
                                    {
                                        title: '功能 1-1-2',
                                    },
                                ],
                            },
                            {
                                expand: false,
                                title: '功能 1-2',
                                children: [
                                    {
                                        title: '功能 1-2-1',
                                    },
                                    {
                                        title: '功能 1-2-1',
                                    },
                                ],
                            },
                            {
                                expand: false,
                                title: '功能 1-2',
                                children: [
                                    {
                                        title: '功能 1-2-1',
                                    },
                                    {
                                        title: '功能 1-2-1',
                                    },
                                ],
                            },
                        ],
                    },
                ],
                filterWord: '1',
                form: {
                    email: '532462837@qq.com',
                    e_mail: '5376@qiye.com',
                    name: '姓名',
                    phone: 1352637247,
                    user_name: 'hsfdius',
                },
                list: [
                    {
                        authority: 111,
                        id: '5435',
                        name: '角色1-1',
                    },
                    {
                        authority: 110,
                        id: '5436',
                        name: '角色1-2',
                    },
                    {
                        authority: 119,
                        id: '5437',
                        name: '角色1-3',
                    },
                    {
                        authority: 118,
                        id: '5438',
                        name: '角色1-4',
                    },
                    {
                        authority: 117,
                        id: '5439',
                        name: '角色1-5',
                    },
                    {
                        authority: 116,
                        id: '5430',
                        name: '角色1-6',
                    },
                    {
                        authority: 115,
                        id: '5431',
                        name: '角色1-7',
                    },
                    {
                        authority: 114,
                        id: '54351',
                        name: '角色1-1',
                    },
                    {
                        authority: 113,
                        id: '54352',
                        name: '角色1-1',
                    },
                    {
                        authority: 111,
                        id: '54353',
                        name: '角色1-1',
                    },
                ],
                loading: false,
                modal: false,
                modalCreate: false,
                organizationName: '',
                rules: {
                    name: [
                        {
                            message: '角色名称不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    authority: [
                        {
                            trigger: 'blur',
                            validator: validatorAuthority,
                        },
                    ],
                },
                selection: [],
                myBarOption: {
                    barColor: 'red',
                },
            };
        },
        methods: {
            changeCurrent(data) {
                const self = this;
                self.deleteModal.name = data.name;
                self.deleteModal.id = data.id;
                self.deleteModal.authority = data.authority;
            },
            changeTreeSelect(data) {
                this.organizationName = data[0].title;
            },
            createUserRole() {
                this.createModal.title = '新增角色';
                this.createModal.name = '';
                this.createModal.authority = null;
                this.modalCreate = true;
            },
            deleteUser() {
                const self = this;
                if (self.deleteModal.id === '') {
                    self.$notice.open({
                        title: '请先选中要删除的角色',
                    });
                } else {
                    self.modal = true;
                }
            },
            editUserRole() {
                const self = this;
                if (self.deleteModal.id === '') {
                    self.$notice.open({
                        title: '请先选中要编辑的角色',
                    });
                } else {
                    self.createModal.title = '编辑角色';
                    self.createModal.name = self.deleteModal.name;
                    self.createModal.authority = self.deleteModal.authority;
                    self.modalCreate = true;
                }
            },
            selectionChange(selection) {
                const self = this;
                self.selection = selection;
            },
            submitCancel() {
                this.modal = false;
            },
            submitCreate() {},
        },
        mounted() {
            this.organizationName = this.departmentList[0].title;
        },
    };
</script>
<template>
    <div class="member-warp">
        <div class="organization-user organization-role">
            <tabs value="name1">
                <tab-pane label="角色管理" name="name1">
                    <card :bordered="false">
                        <div class="top-btn-action">
                            <i-button class="btn-action" type="ghost"
                                      @click.native="createUserRole">+新增角色</i-button>
                            <i-button class="btn-action" type="ghost"
                                      @click.native="editUserRole">编辑</i-button>
                            <i-button class="btn-action" type="ghost"
                                      @click.native="deleteUser">删除</i-button>
                        </div>
                        <row>
                            <i-col span="12" class="left-col-span">
                                <div>
                                    <vue-scrollbar classes="my-scrollbar" ref="Scrollbar">
                                        <div class="scroll-me">
                                            <i-table :columns="columns"
                                                     class="table-list"
                                                     :data="list"
                                                     @on-current-change="changeCurrent"
                                                     ref="list"
                                                     highlight-row>
                                            </i-table>
                                        </div>
                                    </vue-scrollbar>
                                </div>
                                <div class="top-btn-action bottom-btn-action">
                                    <i-button class="btn-action" type="primary">保存</i-button>
                                </div>
                            </i-col>
                            <i-col span="12">
                                <div class="depart-expand-tree">
                                    <h5>权限设置</h5>
                                    <vue-scrollbar classes="my-scrollbar" ref="Scrollbar">
                                        <div class="scroll-me">
                                            <tree :data="departmentList"
                                                  show-checkbox
                                                  @on-select-change="changeTreeSelect"></tree>
                                        </div>
                                    </vue-scrollbar>
                                </div>
                            </i-col>
                        </row>
                    </card>
                </tab-pane>
            </tabs>
        </div>
        <modal
                v-model="modal"
                title="删除" class="setting-modal-delete">
            <div>
                <i-form ref="deleteModal" :model="deleteModal">
                    <row>
                        <i-col class="first-row-title delete-file-tip">
                            <span>确定要删除角色"{{ deleteModal.name }}"吗？
                                删除后不可恢复并且相关部门用户将失去此角色身份。</span>
                        </i-col>
                    </row>
                    <row>
                        <i-col class="btn-group">
                            <i-button type="ghost" class="cancel-btn"
                                      @click.native="submitCancel">取消</i-button>
                            <i-button :loading="loading" type="primary" class="cancel-btn"
                                      @click.native="submitDelete">
                                <span v-if="!loading">确认</span>
                                <span v-else>正在删除…</span>
                            </i-button>
                        </i-col>
                    </row>
                </i-form>
            </div>
        </modal>
        <modal
                v-model="modalCreate"
                :title="createModal.title" class="setting-modal-delete setting-modal-action">
            <div>
                <i-form ref="createModal" :model="createModal" :rules="rules" :label-width="110">
                    <row>
                        <i-col span="14">
                            <form-item label="角色名称" prop="name">
                                <i-input v-model="createModal.name"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="14">
                            <form-item label="权限值" prop="authority">
                                <i-input number v-model="createModal.authority"></i-input>
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