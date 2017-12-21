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
            const self = this;
            return {
                columns: [
                    {
                        align: 'center',
                        type: 'selection',
                        width: 60,
                    },
                    {
                        align: 'center',
                        key: 'name',
                        title: '用户名',
                    },
                    {
                        align: 'center',
                        key: 'id',
                        title: 'ID',
                    },
                    {
                        align: 'center',
                        key: 'sex',
                        render(h, data) {
                            if (data.row.sex === '1') {
                                data.row.sex = '男';
                            } else if (data.row.sex === '0') {
                                data.row.sex = '女';
                            }
                            return data.row.sex;
                        },
                        title: '性别',
                    },
                    {
                        key: 'email',
                        title: '邮箱',
                    },
                    {
                        key: 'phone',
                        title: '手机',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.modalLook = true;
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
                                            self.deleteModal.name = data.row.name;
                                            self.modal1 = true;
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                    style: {
                                        marginLeft: '10px',
                                    },
                                }, '移除'),
                            ]);
                        },
                        title: '操作',
                        width: 180,
                    },
                ],
                deleteModal: {
                    name: '',
                    num: 1,
                    org_name: '',
                },
                departmentList: [
                    {
                        expand: true,
                        title: 'parent 1',
                        children: [
                            {
                                expand: false,
                                title: 'parent 1-1',
                                children: [
                                    {
                                        expand: false,
                                        title: 'leaf 1-1-1',
                                        children: [
                                            {
                                                title: 'leaf 1-1-1-1',
                                            },
                                            {
                                                title: 'leaf 1-1-1-2',
                                            },
                                        ],
                                    },
                                    {
                                        title: 'leaf 1-1-2',
                                    },
                                ],
                            },
                            {
                                expand: false,
                                title: 'parent 1-2',
                                children: [
                                    {
                                        title: 'leaf 1-2-1',
                                    },
                                    {
                                        title: 'leaf 1-2-1',
                                    },
                                ],
                            },
                            {
                                expand: false,
                                title: 'parent 1-2',
                                children: [
                                    {
                                        title: 'leaf 1-2-1',
                                    },
                                    {
                                        title: 'leaf 1-2-1',
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
                        email: '226458751@qq.com',
                        id: '5435',
                        name: 'gdeyf',
                        phone: '1876534576',
                        sex: '1',
                    },
                    {
                        email: '226458751@qq.com',
                        id: '5435',
                        name: 'gdeyf',
                        phone: '1876534576',
                        sex: '0',
                    },
                    {
                        email: '226458751@qq.com',
                        id: '5435',
                        name: 'gdeyf',
                        phone: '1876534576',
                        sex: '1',
                    },
                    {
                        email: '226458751@qq.com',
                        id: '5435',
                        name: 'gdeyf',
                        phone: '1876534576',
                        sex: '1',
                    },
                    {
                        email: '226458751@qq.com',
                        id: '5435',
                        name: 'gdeyf',
                        phone: '1876534576',
                        sex: '1',
                    },
                    {
                        email: '226458751@qq.com',
                        id: '5435',
                        name: 'gdeyf',
                        phone: '1876534576',
                        sex: '1',
                    },
                    {
                        email: '226458751@qq.com',
                        id: '5435',
                        name: 'gdeyf',
                        phone: '1876534576',
                        sex: '1',
                    },
                    {
                        email: '226458751@qq.com',
                        id: '5435',
                        name: 'gdeyf',
                        phone: '1876534576',
                        sex: '1',
                    },
                    {
                        email: '226458751@qq.com',
                        id: '5435',
                        name: 'gdeyf',
                        phone: '1876534576',
                        sex: '1',
                    },
                ],
                loading: false,
                modal1: false,
                modal2: false,
                modalLook: false,
                organizationName: '',
                pagination: {
                    count: 3,
                    current: 1,
                    paginate: 2,
                },
                searchList: [
                    {
                        label: '用户名称',
                        value: '1',
                    },
                    {
                        label: '用户ID',
                        value: '2',
                    },
                ],
                searchValue: '',
                selection: [],
            };
        },
        methods: {
            batchRemove() {
                const self = this;
                const deletes = [];
                self.selection.forEach(item => {
                    deletes.push(item.id);
                });
                if (deletes.length < 1) {
                    self.$notice.open({
                        title: '请选择要移除的用户!',
                    });
                } else {
                    self.deleteModal.org_name = self.organizationName;
                    self.deleteModal.num = deletes.length;
                    self.modal2 = true;
                }
            },
            changePage() {},
            changeTreeSelect(data) {
                this.organizationName = data[0].title;
            },
            selectionChange(selection) {
                const self = this;
                self.selection = selection;
            },
            submitCancel(data) {
                if (data === 1) {
                    this.modal1 = false;
                }
                if (data === 2) {
                    this.modal2 = false;
                }
            },
        },
        mounted() {
            this.organizationName = this.departmentList[0].title;
        },
    };
</script>
<template>
    <div class="member-warp">
        <div class="organization-user">
            <tabs value="name1">
                <tab-pane label="部门用户" name="name1">
                    <card :bordered="false">
                        <row>
                            <i-col span="10" class="left-col-span">
                                <div class="depart-expand-tree">
                                    <h5>部门名称</h5>
                                    <vue-scrollbar classes="my-scrollbar" ref="Scrollbar">
                                        <div class="scroll-me">
                                            <tree :data="departmentList"
                                                  @on-select-change="changeTreeSelect"></tree>
                                        </div>
                                    </vue-scrollbar>
                                </div>
                            </i-col>
                            <i-col span="14">
                                <div class="top-btn-action">
                                    <router-link to="/member/organization/user/create">
                                        <i-button class="btn-action" type="ghost">+添加用户</i-button>
                                    </router-link>
                                    <i-button class="btn-action" @click.native="batchRemove"
                                              type="ghost">批量删除</i-button>
                                    <div class="goods-body-header-right">
                                        <i-input v-model="searchValue" placeholder="请输入关键词进行搜索">
                                            <i-select v-model="filterWord" slot="prepend" style="width: 100px;">
                                                <i-option v-for="item in searchList"
                                                          :value="item.value">{{ item.label }}</i-option>
                                            </i-select>
                                            <i-button slot="append" type="primary">搜索</i-button>
                                        </i-input>
                                    </div>
                                </div>
                                <i-table :columns="columns"
                                         table-list
                                         :data="list"
                                         @on-selection-change="selectionChange"
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
                            </i-col>
                        </row>
                    </card>
                </tab-pane>
            </tabs>
        </div>
        <modal
                v-model="modal1"
                title="移除" class="setting-modal-delete">
            <div>
                <i-form ref="deleteModal" :model="deleteModal" :label-width="120">
                    <row>
                        <i-col class="first-row-title delete-file-tip">
                            <span>确定将"{{ deleteModal.name }}"从部门移除吗？</span>
                        </i-col>
                    </row>
                    <row>
                        <i-col class="btn-group">
                            <i-button type="ghost" class="cancel-btn"
                                      @click.native="submitCancel(1)">取消</i-button>
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
                v-model="modal2"
                title="批量移除" class="setting-modal-delete">
            <div>
                <i-form ref="deleteModal" :model="deleteModal" :label-width="120">
                    <row>
                        <i-col class="first-row-title delete-file-tip">
                            <span>确定移除部门"{{ deleteModal.org_name }}下的{{ deleteModal.num }}位用户吗"？</span>
                        </i-col>
                    </row>
                    <row>
                        <i-col class="btn-group">
                            <i-button type="ghost" class="cancel-btn"
                                      @click.native="submitCancel(2)">取消</i-button>
                            <i-button :loading="loading" type="primary" class="cancel-btn"
                                      @click.native="batchRemovesure">
                                <span v-if="!loading">确认</span>
                                <span v-else>正在删除…</span>
                            </i-button>
                        </i-col>
                    </row>
                </i-form>
            </div>
        </modal>
        <modal
                v-model="modalLook"
                title="查看详细信息" class="setting-modal-delete user-detail-message">
            <div>
                <i-form ref="form" :model="form" :label-width="160">
                    <row>
                        <i-col>
                            <form-item label="用户名">
                                <span>{{ form.user_name }}</span>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col>
                            <form-item label="姓名">
                                <span>{{ form.name }}</span>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col>
                            <form-item label="个人邮箱">
                                <span>{{ form.email }}</span>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col>
                            <form-item label="企业邮箱">
                                <span>{{ form.e_mail }}</span>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col>
                            <form-item label="手机">
                                <span>{{ form.phone }}</span>
                            </form-item>
                        </i-col>
                    </row>
                </i-form>
            </div>
        </modal>
    </div>
</template>