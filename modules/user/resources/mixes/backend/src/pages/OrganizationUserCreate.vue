<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(vm => {
                vm.parent.org_name = to.query.org_name;
            });
        },
        data() {
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
                            if (data.row.status === true) {
                                return h('div', [
                                    h('i-button', {
                                        on: {
                                            click() {},
                                        },
                                        props: {
                                            size: 'small',
                                            type: 'ghost',
                                        },
                                    }, '添加'),
                                ]);
                            }
                            if (data.row.status === false) {
                                return h('div', '已添加');
                            }
                            return '';
                        },
                        title: '操作',
                        width: 180,
                    },
                ],
                filterWord: '1',
                list: [
                    {
                        email: '226458751@qq.com',
                        id: '5435',
                        name: 'gdeyf',
                        phone: '1876534576',
                        sex: '1',
                        status: false,
                    },
                    {
                        email: '226458751@qq.com',
                        id: '5435',
                        name: 'gdeyf',
                        phone: '1876534576',
                        sex: '2',
                        status: true,
                    },
                    {
                        email: '226458751@qq.com',
                        id: '5435',
                        name: 'gdeyf',
                        phone: '1876534576',
                        sex: '1',
                        status: true,
                    },
                    {
                        email: '226458751@qq.com',
                        id: '5435',
                        name: 'gdeyf',
                        phone: '1876534576',
                        sex: '1',
                        status: true,
                    },
                ],
                loading: false,
                pagination: {
                    count: 3,
                    current: 1,
                    paginate: 2,
                },
                parent: {
                    org_name: '',
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
            batchAdd() {},
            changePage() {},
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
        },
    };
</script>
<template>
    <div class="member-warp">
        <div class="organization-user-create">
            <div class="return-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>"{{ parent.org_name }}"添加用户</span>
            </div>
            <card :bordered="false">
                <div class="top-tip-content">
                    <p>提示</p>
                    <p>添加至当前部门的用户会自动继承部门的所有功能权限</p>
                </div>
                <div class="top-btn-action">
                    <i-button class="btn-action" type="ghost" @click.native="batchAdd">添加用户</i-button>
                    <i-button class="btn-action" type="ghost">刷新</i-button>
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
                         :data="list"
                         @on-selection-change="selection"
                         ref="list"
                         highlight-row>
                </i-table>
                <div class="ivu-page-wrap">
                    <page :current="pagination.current"
                          :page-size="pagination.paginate"
                          :total="pagination.count"
                          @on-change="changePage1"
                          show-elevator></page>
                </div>
            </card>
        </div>
    </div>
</template>