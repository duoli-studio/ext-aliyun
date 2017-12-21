<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(vm => {
                vm.parent.tab_name = to.query.tab_name;
                vm.parent.fun_name = to.query.fun_name;
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
                createModal: {
                    function_name: '',
                    parent_fun: '',
                },
                deleteModal: {
                    name: 'ibenchu',
                },
                list: [
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
                loading: false,
                modal: false,
                modalCreate: false,
                pagination: {
                    count: 3,
                    current: 1,
                    paginate: 2,
                },
                parent: {
                    fun_name: '',
                    tab_name: '',
                },
                rules: {
                    function_name: [
                        {
                            message: '功能不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                },
            };
        },
        methods: {
            changePage() {},
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
            submitCancel() {
                this.modal = false;
            },
        },
    };
</script>
<template>
    <div class="member-warp">
        <div class="function-manager-look">
            <div class="return-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>查看"{{ parent.tab_name}}-{{ parent.fun_name }}"功能</span>
            </div>
            <card :bordered="false">
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