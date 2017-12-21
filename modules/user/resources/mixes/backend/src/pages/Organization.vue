<script>
    export default {
        beforeRouteEnter(to, from, next) {
            const data = {};
            if (to.query.parent_id) {
                data.parent.last_id = to.query.parent_id;
            }
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                columns: [
                    {
                        align: 'center',
                        key: 'id',
                        title: 'ID',
                        width: 160,
                    },
                    {
                        align: 'center',
                        key: 'name',
                        title: '部门名称',
                        width: 200,
                    },
                    {
                        key: 'role',
                        title: '部门角色',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.level = 2;
                                            self.$router.push({
                                                path: '/member/organization',
                                                query: {
                                                    parent_id: data.row.id,
                                                },
                                            });
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '查看'),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.createModal.title = '新增下级';
                                            self.modalCreate = true;
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                    style: {
                                        marginLeft: '10px',
                                    },
                                }, '新增下级'),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.createModal.title = '编辑部门';
                                            self.modalCreate = true;
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                    style: {
                                        marginLeft: '10px',
                                    },
                                }, '编辑'),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.deleteModal.name = data.row.name;
                                            self.modal = true;
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                    style: {
                                        marginLeft: '10px',
                                    },
                                }, '删除'),
                            ]);
                        },
                        title: '操作',
                        width: 360,
                    },
                ],
                createModal: {
                    org_name: '',
                    parent_org: '',
                    title: '',
                },
                deleteModal: {
                    name: 'ibenchu',
                },
                departmentList: [
                    {
                        children: [
                            {
                                label: '部门1-1',
                                value: '11',
                            },
                            {
                                label: '部门1-2',
                                value: '12',
                            },
                        ],
                        label: '部门1',
                        value: '1',
                    },
                    {
                        children: [
                            {
                                children: [
                                    {
                                        label: '部门2-1-1',
                                        value: '211',
                                    },
                                ],
                                label: '部门2-1',
                                value: '21',
                            },
                            {
                                label: '部门2-2',
                                value: '22',
                            },
                        ],
                        label: '部门2',
                        value: '2',
                    },
                ],
                level: 1,
                list: [
                    {
                        id: 231,
                        name: '本初',
                        role: '管理员',
                    },
                    {
                        id: 656,
                        name: '本初2',
                        role: '管理员',
                    },
                    {
                        id: 989,
                        name: '本初3',
                        role: '管理员',
                    },
                ],
                loading: false,
                modal: false,
                modalCreate: false,
                pagination: {
                    count: 3,
                    current: 1,
                    paginate: 2,
                },
                parent: {
                    last_id: '',
                    last_name: '',
                },
                rules: {
                    org_name: [
                        {
                            message: '部门名称不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                },
            };
        },
        methods: {
            createOrganization() {
                this.createModal.title = '新增部门';
                this.modalCreate = true;
            },
            filterDepartment(data) {
                window.console.log(data);
            },
            goBack() {
                const self = this;
                self.$router.go(-1);
                self.level = 1;
            },
            submitCancel() {
                this.modal = false;
            },
        },
    };
</script>
<template>
    <div class="member-warp">
        <div class="organization-manager">
            <div class="return-link-title tab-pane-title" v-if="level === 1">
                <span>组织部门</span>
            </div>
            <div class="return-link-title"  v-if="level === 2">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>查看"{{ parent.last_name}}"部门</span>
            </div>
            <card :bordered="false">
                <div class="top-btn-action">
                    <i-button class="btn-action" type="ghost"
                    @click.native="createOrganization">+新增部门</i-button>
                    <i-button class="btn-action" type="ghost">刷新</i-button>
                </div>
                <i-table :columns="columns"
                         :data="list"
                         ref="list"
                         highlight-row>
                </i-table>
                <div class="ivu-page-wrap">
                    <page :current="pagination.current"
                          :page-size="pagination.paginate"
                          :total="pagination.count"
                          @on-change="changePage"
                          show-elevator></page>
                </div>
            </card>
        </div>
        <modal
                v-model="modal"
                title="删除" class="setting-modal-delete">
            <div>
                <i-form ref="deleteModal" :model="deleteModal" :label-width="120">
                    <row>
                        <i-col class="first-row-title delete-file-tip">
                            <span>确定要删除"{{ deleteModal.name }}"吗？删除后不可恢复并且相关人员将失去组织部门。</span>
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
                            <form-item label="部门名称" prop="org_name">
                                <i-input v-model="createModal.org_name"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="14">
                            <form-item label="上级部门" prop="parent_org">
                                <cascader :data="departmentList"
                                          change-on-select
                                          @on-change="filterDepartment"
                                          v-model="createModal.parent_org"></cascader>
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