<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                businessColumns: [
                    {
                        align: 'center',
                        key: 'id',
                        title: 'ID',
                        width: 160,
                    },
                    {
                        key: 'name',
                        title: '功能名称',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.$router.push({
                                                path: '/member/function/manager/look',
                                                query: {
                                                    tab_name: self.tab_name,
                                                    fun_name: data.row.name,
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
                                            self.$router.push({
                                                path: '/member/function/manager/set',
                                                query: {
                                                    tab_name: self.tab_name,
                                                    fun_name: data.row.name,
                                                },
                                            });
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                    style: {
                                        marginLeft: '10px',
                                    },
                                }, '设置'),
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
                        width: 280,
                    },
                ],
                businessData: [
                    {
                        id: 1323,
                        name: 'benchu1',
                    },
                    {
                        id: 5677,
                        name: 'benchu2',
                    },
                    {
                        id: 8684,
                        name: 'benchu3',
                    },
                ],
                createModal: {
                    function_name: '',
                    parent_fun: '',
                },
                deleteModal: {
                    name: 'ibenchu',
                },
                forumColumns: [
                    {
                        align: 'center',
                        key: 'id',
                        title: 'ID',
                        width: 160,
                    },
                    {
                        key: 'name',
                        title: '功能名称',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.$router.push({
                                                path: '/member/function/manager/look',
                                                query: {
                                                    tab_name: self.tab_name,
                                                    fun_name: data.row.name,
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
                                            self.$router.push({
                                                path: '/member/function/manager/set',
                                                query: {
                                                    tab_name: self.tab_name,
                                                    fun_name: data.row.name,
                                                },
                                            });
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                    style: {
                                        marginLeft: '10px',
                                    },
                                }, '设置'),
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
                        width: 280,
                    },
                ],
                forumData: [
                    {
                        id: 1323,
                        name: 'benchu1',
                    },
                    {
                        id: 5677,
                        name: 'benchu2',
                    },
                    {
                        id: 8684,
                        name: 'benchu3',
                    },
                ],
                functionList: [
                    {
                        label: '功能1',
                        value: '1',
                    },
                    {
                        label: '功能2',
                        value: '2',
                    },
                ],
                loading: false,
                mallColumns: [
                    {
                        align: 'center',
                        key: 'id',
                        title: 'ID',
                        width: 160,
                    },
                    {
                        key: 'name',
                        title: '功能名称',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.$router.push({
                                                path: '/member/function/manager/look',
                                                query: {
                                                    tab_name: self.tab_name,
                                                    fun_name: data.row.name,
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
                                            self.$router.push({
                                                path: '/member/function/manager/set',
                                                query: {
                                                    tab_name: self.tab_name,
                                                    fun_name: data.row.name,
                                                },
                                            });
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                    style: {
                                        marginLeft: '10px',
                                    },
                                }, '设置'),
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
                        width: 280,
                    },
                ],
                mallData: [
                    {
                        id: 1323,
                        name: '商品管理',
                    },
                    {
                        id: 5677,
                        name: 'benchu2',
                    },
                    {
                        id: 8684,
                        name: 'benchu3',
                    },
                ],
                modal: false,
                modalCreate: false,
                pagination1: {
                    count: 3,
                    current: 1,
                    paginate: 2,
                },
                pagination2: {
                    count: 3,
                    current: 1,
                    paginate: 2,
                },
                pagination3: {
                    count: 3,
                    current: 1,
                    paginate: 2,
                },
                tab_name: '商城',
                rules: {
                    function_name: [
                        {
                            message: '功能不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                },
                selection1: [],
                selection2: [],
            };
        },
        methods: {
            changePage1() {},
            changePage2() {},
            changePage3() {},
            changeTab(pro) {
                const self = this;
                if (pro === 'name1') {
                    self.tab_name = '商城';
                } else if (pro === 'name2') {
                    self.tab_name = '商家';
                } else if (pro === 'name3') {
                    self.tab_name = '论坛';
                }
            },
            createFunction() {
                this.modalCreate = true;
            },
            submitCancel() {
                this.modal = false;
            },
            submitCreate() {},
            submitDelete() {},
        },
    };
</script>
<template>
    <div class="member-warp">
        <div class="function-manager">
            <tabs value="name1" @on-click="changeTab">
                <tab-pane label="商城" name="name1">
                    <card :bordered="false">
                        <div class="top-btn-action">
                            <i-button class="btn-action" type="ghost"
                            @click.native="createFunction">+新增功能</i-button>
                            <i-button class="btn-action" type="ghost">刷新</i-button>
                        </div>
                        <i-table :columns="mallColumns"
                                 :data="mallData"
                                 ref="mall"
                                 highlight-row>
                        </i-table>
                        <div class="ivu-page-wrap">
                            <page :current="pagination1.current"
                                  :page-size="pagination1.paginate"
                                  :total="pagination1.count"
                                  @on-change="changePage1"
                                  show-elevator></page>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="商家" name="name2">
                    <card :bordered="false">
                        <div class="top-btn-action">
                            <i-button class="btn-action" type="ghost">+新增功能</i-button>
                            <i-button class="btn-action" type="ghost">刷新</i-button>
                        </div>
                        <i-table :columns="businessColumns"
                                 :data="businessData"
                                 ref="business"
                                 highlight-row>
                        </i-table>
                        <div class="ivu-page-wrap">
                            <page :current="pagination2.current"
                                  :page-size="pagination2.paginate"
                                  :total="pagination2.count"
                                  @on-change="changePage2"
                                  show-elevator></page>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="论坛" name="name3">
                    <card :bordered="false">
                        <div class="top-btn-action">
                            <i-button class="btn-action" type="ghost">+新增功能</i-button>
                            <i-button class="btn-action" type="ghost">刷新</i-button>
                        </div>
                        <i-table :columns="forumColumns"
                                 :data="forumData"
                                 ref="forum"
                                 highlight-row>
                        </i-table>
                        <div class="ivu-page-wrap">
                            <page :current="pagination3.current"
                                  :page-size="pagination3.paginate"
                                  :total="pagination3.count"
                                  @on-change="changePage3"
                                  show-elevator></page>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
            <modal
                    v-model="modal"
                    title="删除" class="setting-modal-delete">
                <div>
                    <i-form ref="deleteModal" :model="deleteModal" :label-width="120">
                        <row>
                            <i-col class="first-row-title delete-file-tip">
                                <span>确定要删除"{{ deleteModal.name }}"功能吗？删除后拥有此功能的角色将失去此功能。</span>
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
                    title="新增功能" class="setting-modal-delete setting-modal-action">
                <div>
                    <i-form ref="createModal" :model="createModal" :rules="rules" :label-width="110">
                        <row>
                            <i-col span="14">
                                <form-item label="功能名称" prop="function_name">
                                    <i-input v-model="createModal.function_name"></i-input>
                                </form-item>
                            </i-col>
                        </row>
                        <row>
                            <i-col span="14">
                                <form-item label="父级功能" prop="parent_fun">
                                    <i-select v-model="createModal.parent_fun">
                                        <i-option v-for="item in functionList"
                                                  :value="item.value">{{ item.label }}
                                        </i-option>
                                    </i-select>
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
    </div>
</template>