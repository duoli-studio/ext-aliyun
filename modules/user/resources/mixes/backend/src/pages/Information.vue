<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/member/administration/information/list`, {
                order: 'asc',
                sort: 'order',
            }).then(response => {
                const { data, pagination } = response.data;
                next(vm => {
                    data.forEach(item => {
                        item.loading = false;
                    });
                    vm.list = data;
                    vm.pagination = pagination;
                    injection.loading.finish();
                });
            }).catch(() => {
                injection.loading.error();
            });
        },
        data() {
            const self = this;
            return {
                columns: [
                    {
                        key: 'name',
                        title: '信息项名称',
                        width: 300,
                    },
                    {
                        align: 'center',
                        key: 'order',
                        render(h, data) {
                            const store = data.row;
                            return h('i-input', {
                                on: {
                                    'on-change': event => {
                                        store.order = event.target.value;
                                    },
                                    'on-blur': () => {
                                        self.update(store);
                                    },
                                },
                                props: {
                                    value: self.list[data.index].order,
                                },
                            });
                        },
                        title: '显示顺序',
                        width: 120,
                    },
                    {
                        align: 'center',
                        key: 'register',
                        render(h, data) {
                            return h('checkbox', {
                                on: {
                                    input(value) {
                                        data.row.register = value;
                                        self.update(data.row);
                                    },
                                },
                                props: {
                                    value: self.list[data.index].register,
                                },
                            });
                        },
                        title: '注册是否显示',
                        width: 120,
                    },
                    {
                        align: 'center',
                        key: 'details',
                        render(h, data) {
                            return h('checkbox', {
                                on: {
                                    input(value) {
                                        data.row.details = value;
                                        self.update(data.row);
                                    },
                                },
                                props: {
                                    value: self.list[data.index].details,
                                },
                            });
                        },
                        title: '资料页是否显示',
                        width: 120,
                    },
                    {
                        align: 'center',
                        key: 'required',
                        render(h, data) {
                            return h('checkbox', {
                                on: {
                                    input(value) {
                                        data.row.required = value;
                                        self.update(data.row);
                                    },
                                },
                                props: {
                                    value: self.list[data.index].required,
                                },
                            });
                        },
                        title: '是否必填',
                        width: 120,
                    },
                    {
                        key: 'none',
                        title: ' ',
                    },
                    {
                        key: 'handle',
                        render(h, data) {
                            let text;
                            if (self.list[data.index].loading) {
                                text = injection.trans('content.global.delete.loading');
                            } else {
                                text = injection.trans('content.global.delete.submit');
                            }
                            return h('div', [
                                h('router-link', {
                                    props: {
                                        to: `/member/information/${data.row.id}/edit`,
                                    },
                                }, [
                                    h('i-button', {
                                        props: {
                                            size: 'small',
                                            type: 'default',
                                        },
                                    }, '编辑'),
                                ]),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.remove(data.index);
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'error',
                                    },
                                    style: {
                                        marginLeft: '10px',
                                    },
                                }, [
                                    h('span', text),
                                ]),
                            ]);
                        },
                        title: '操作',
                        width: 300,
                    },
                ],
                list: [],
                pagination: {},
                self: this,
            };
        },
        methods: {
            remove(index) {
                const self = this;
                const information = self.list[index];
                information.loading = true;
                self.$http.post(`${window.api}/member/administration/information/remove`, {
                    id: information.id,
                }).then(() => {
                    self.$notice.open({
                        title: '删除信息项成功！',
                    });
                    self.refresh();
                }).catch(() => {
                    self.$notice.error({
                        title: '删除信息项失败！',
                    });
                }).finally(() => {
                    information.loading = false;
                });
            },
            refresh() {
                const self = this;
                self.$notice.open({
                    title: '正在刷新数据...',
                });
                self.$loading.start();
                self.$http.post(`${window.api}/member/administration/information/list`, {
                    order: 'asc',
                    sort: 'order',
                }).then(response => {
                    const { data, pagination } = response.data;
                    data.forEach(item => {
                        item.loading = false;
                    });
                    self.$loading.finish();
                    self.$notice.open({
                        title: '刷新数据完成！',
                    });
                    self.list = data;
                    self.pagination = pagination;
                }).catch(() => {
                    self.$loading.error();
                });
            },
            update(data) {
                const self = this;
                self.$notice.open({
                    title: '更新信息项',
                });
                self.$loading.start();
                self.$http.post(`${window.api}/member/administration/information/edit`, data).then(() => {
                    self.$loading.finish();
                    self.$notice.open({
                        title: '更新信息项数据成功！',
                    });
                    self.refresh();
                }).catch(() => {
                    self.$loading.fail();
                    self.$notice.error({
                        title: '更新信息项数据失败！',
                    });
                });
            },
        },
    };
</script>
<template>
    <div class="member-wrap">
        <div class="user-information">
            <card :bordered="false">
                <template slot="title">
                    <span class="text">信息列表</span>
                    <router-link class="extend" to="/member/information/create">
                        <i-button type="default">添加信息项</i-button>
                    </router-link>
                </template>
                <i-table :columns="columns" :context="self" :data="list"></i-table>
                <div class="ivu-page-wrap">
                    <page :current="pagination.current"
                          :page-size="pagination.paginate"
                          :total="pagination.total"
                          @on-change="paginator"></page>
                </div>
            </card>
        </div>
    </div>
</template>