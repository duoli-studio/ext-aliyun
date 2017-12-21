<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
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
                        title: '姓名',
                    },
                    {
                        align: 'center',
                        key: 'user_name',
                        title: '用户名',
                    },
                    {
                        align: 'center',
                        key: 'id',
                        title: 'ID',
                    },
                    {
                        align: 'center',
                        key: 'status',
                        render(h, data) {
                            if (data.row.status) {
                                return h('span', [
                                    h('icon', {
                                        props: {
                                            type: 'checkmark-circled',
                                            color: '#02a845',
                                        },
                                    }),
                                ]);
                            }
                            return h('span', [
                                h('icon', {
                                    props: {
                                        type: 'close-circled',
                                        color: '#ff3300',
                                        size: '16px',
                                    },
                                }),
                            ]);
                        },
                        title: '状态',
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
                                            self.$router.push({
                                                path: '/member/user/manager/forbid',
                                                query: {
                                                    id: data.row.id,
                                                },
                                            });
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '封禁'),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.$router.push({
                                                path: '/member/user/manager/create',
                                                query: {
                                                    type: '1',
                                                    name: data.row.name,
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
                                }, '编辑'),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.deleteModal.name = data.row.name;
                                            self.deleteModal.type = 0;
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
                                }, '删除'),
                            ]);
                        },
                        title: '操作',
                        width: 280,
                    },
                ],
                deleteModal: {
                    name: 'ibenchu',
                    num: 2,
                    mode: 0,
                    type: 0,
                },
                list: [
                    {
                        email: '3265256564@ibenchu.com',
                        id: 1323,
                        name: 'benchu',
                        phone: 3137264,
                        status: true,
                        user_name: 'admin',
                    },
                    {
                        email: '3265256564@ibenchu.com',
                        id: 1323,
                        name: 'benchu',
                        phone: 3137264,
                        status: true,
                        user_name: 'admin',
                    },
                    {
                        email: '3265256564@ibenchu.com',
                        id: 1323,
                        name: 'benchu',
                        phone: 3137264,
                        status: false,
                        user_name: 'admin',
                    },
                ],
                msgColumns: [
                    {
                        align: 'center',
                        type: 'selection',
                        width: 60,
                    },
                    {
                        align: 'center',
                        key: 'sort',
                        render(h, data) {
                            const { row } = data.row;
                            return h('tooltip', {
                                props: {
                                    placement: 'right-end',
                                },
                                scopedSlots: {
                                    content() {
                                        return '回车更新数据';
                                    },
                                    default() {
                                        return h('i-input', {
                                            on: {
                                                'on-change': event => {
                                                    row.sort = event.target.value;
                                                },
                                                'on-enter': () => {
                                                    self.update(row);
                                                },
                                            },
                                            props: {
                                                value: self.msgData[data.index].sort,
                                            },
                                            style: {
                                                width: '84px',
                                            },
                                        });
                                    },
                                },
                            });
                        },
                        title: '排序',
                        width: 160,
                    },
                    {
                        align: 'center',
                        key: 'name',
                        title: '信息项名称',
                        width: 200,
                    },
                    {
                        key: 'enabled',
                        render(h, data) {
                            const { row } = data.row;
                            return h('checkbox', {
                                on: {
                                    'on-change': value => {
                                        row.enabled = value;
                                    },
                                },
                            });
                        },
                        title: '是否必填',
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
                                                path: '/member/user/manager/message',
                                                query: {
                                                    type: '1',
                                                },
                                            });
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '编辑'),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.deleteModal.name = data.row.name;
                                            self.deleteModal.type = 1;
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
                                }, '删除'),
                            ]);
                        },
                        title: '操作',
                        width: 180,
                    },
                ],
                msgData: [
                    {
                        enabled: false,
                        id: 123,
                        name: '视频链接',
                        sort: null,
                    },
                    {
                        enabled: false,
                        id: 124,
                        name: '视频链接',
                        sort: null,
                    },
                ],
                modal1: false,
                modal2: false,
                page1: {
                    count: 3,
                    current: 1,
                    paginate: 2,
                },
                page2: {
                    count: 3,
                    current: 1,
                    paginate: 2,
                },
                page3: {
                    count: 3,
                    current: 1,
                    paginate: 2,
                },
                recycling: [
                    {
                        align: 'center',
                        type: 'selection',
                        width: 60,
                    },
                    {
                        align: 'center',
                        key: 'name',
                        title: '姓名',
                        width: 120,
                    },
                    {
                        align: 'center',
                        key: 'user_name',
                        title: '用户名',
                        width: 140,
                    },
                    {
                        align: 'center',
                        key: 'id',
                        title: 'ID',
                        width: 140,
                    },
                    {
                        key: 'email',
                        title: '邮箱',
                        width: 240,
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
                                        click() {},
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '还原'),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.deleteModal.name = data.row.name;
                                            self.deleteModal.type = 0;
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
                                }, '删除'),
                            ]);
                        },
                        title: '操作',
                        width: 180,
                    },
                ],
                recyclingData: [
                    {
                        email: '3265256564@ibenchu.com',
                        id: 1323,
                        name: 'benchu',
                        phone: 3137264,
                        user_name: 'admin',
                    },
                    {
                        email: '3265256564@ibenchu.com',
                        id: 1323,
                        name: 'benchu',
                        phone: 3137264,
                        user_name: 'admin',
                    },
                    {
                        email: '3265256564@ibenchu.com',
                        id: 1323,
                        name: 'benchu',
                        phone: 3137264,
                        user_name: 'admin',
                    },
                ],
                selection1: [],
                selection2: [],
                selection3: [],
            };
        },
        methods: {
            changePage1() {},
            changePage2() {},
            batchRemove(pre) {
                const self = this;
                const deletes = [];
                const deletesRet = [];
                const deletesMeg = [];
                if (pre === 1) {
                    self.deleteModal.mode = -1;
                    self.selection1.forEach(item => {
                        deletes.push(item.id);
                    });
                    self.deleteModal.num = deletes.length;
                    if (deletes.length < 1) {
                        self.$notice.open({
                            title: '请选择要删除的用户!',
                        });
                    } else {
                        self.modal2 = true;
                    }
                }
                if (pre === 2) {
                    self.deleteModal.mode = -2;
                    self.selection2.forEach(item => {
                        deletesRet.push(item.id);
                    });
                    self.deleteModal.num = deletesRet.length;
                    if (deletesRet.length < 1) {
                        self.$notice.open({
                            title: '请选择要删除的用户!',
                        });
                    } else {
                        self.modal2 = true;
                    }
                }
                if (pre === 3) {
                    self.deleteModal.mode = 1;
                    self.selection3.forEach(item => {
                        deletesMeg.push(item.id);
                    });
                    self.deleteModal.num = deletesMeg.length;
                    if (deletesMeg.length < 1) {
                        self.$notice.open({
                            title: '请选择要删除的信息!',
                        });
                    } else {
                        self.modal2 = true;
                    }
                }
            },
            submitCancel(data) {
                if (data === 1) {
                    this.modal1 = false;
                }
                if (data === 2) {
                    this.modal2 = false;
                }
            },
            submitDelete() {},
            selectionChange1(selection) {
                const self = this;
                self.selection1 = selection;
            },
            selectionChange2(selection) {
                const self = this;
                self.selection2 = selection;
            },
            selectionChange3(selection) {
                const self = this;
                self.selection3 = selection;
            },
            batchRemovesure() {},
            update() {},
        },
    };
</script>
<template>
    <div class="member-warp">
        <div class="user-manager">
            <tabs value="name1">
                <tab-pane label="用户管理" name="name1">
                    <card :bordered="false">
                        <div class="top-btn-action">
                            <router-link :to="{
                                path: '/member/user/manager/create',
                                query: {
                                    type: '0',
                                },
                            }">
                                <i-button class="btn-action" type="ghost">+新增角色</i-button>
                            </router-link>
                            <i-button class="btn-action" @click.native="batchRemove(1)"
                                      type="ghost">批量删除</i-button>
                            <i-button class="btn-action" type="ghost">刷新</i-button>
                        </div>
                        <i-table :columns="columns"
                                 :data="list"
                                 @on-selection-change="selectionChange1"
                                 ref="list"
                                 highlight-row>
                        </i-table>
                        <div class="ivu-page-wrap">
                            <page :current="page1.current"
                                  :page-size="page1.paginate"
                                  :total="page1.count"
                                  @on-change="changePage1"
                                  show-elevator></page>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="回收站" name="name2">
                    <card :bordered="false">
                        <div class="top-btn-action">
                            <i-button class="btn-action" type="ghost">批量还原</i-button>
                            <i-button class="btn-action" @click.native="batchRemove(2)"
                                      type="ghost">批量删除</i-button>
                            <i-button class="btn-action" type="ghost">刷新</i-button>
                        </div>
                        <i-table :columns="recycling"
                                 :data="recyclingData"
                                 @on-selection-change="selectionChange2"
                                 ref="recycling"
                                 highlight-row>
                        </i-table>
                        <div class="ivu-page-wrap">
                            <page :current="page2.current"
                                  :page-size="page2.paginate"
                                  :total="page2.count"
                                  @on-change="changePage2"
                                  show-elevator></page>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="信息管理" name="name3">
                    <card :bordered="false">
                        <div class="top-btn-action">
                            <router-link :to="{
                                path: '/member/user/manager/message',
                                query: {
                                    type: '0',
                                },
                            }">
                                <i-button class="btn-action" type="ghost">+新增信息</i-button>
                            </router-link>
                            <i-button class="btn-action" @click.native="batchRemove(3)"
                                      type="ghost">批量删除</i-button>
                            <i-button class="btn-action" type="ghost">刷新</i-button>
                        </div>
                        <i-table :columns="msgColumns"
                                 :data="msgData"
                                 @on-selection-change="selectionChange3"
                                 ref="list"
                                 highlight-row>
                        </i-table>
                        <div class="ivu-page-wrap">
                            <page :current="page3.current"
                                  :page-size="page3.paginate"
                                  :total="page3.count"
                                  @on-change="changePage3"
                                  show-elevator></page>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
            <modal
                    v-model="modal1"
                    title="删除" class="setting-modal-delete">
                <div>
                    <i-form ref="deleteModal" :model="deleteModal" :label-width="120">
                        <row>
                            <i-col class="first-row-title delete-file-tip">
                                <span v-if="deleteModal.type !== 1">
                                    确定要删除用户"{{ deleteModal.name }}"吗？</span>
                                <span v-if="deleteModal.type === 1">
                                    确定要删除信息项"{{ deleteModal.name }}"吗？</span>
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
                    title="删除" class="setting-modal-delete">
                <div>
                    <i-form ref="deleteModal" :model="deleteModal" :label-width="120">
                        <row>
                            <i-col class="first-row-title delete-file-tip">
                                <span v-if="deleteModal.mode === -1 || deleteModal.mode === -2">
                                    确定要删除这{{ deleteModal.num }}个用户吗？
                                </span>
                                <span v-if="deleteModal.mode === 1">
                                    确定要删除这{{ deleteModal.num }}项信息吗？
                                </span>
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
        </div>
    </div>
</template>