<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/member/administration/information/group/list`, {
                order: 'asc',
                sort: 'order',
            }).then(response => {
                const { data, pagination } = response.data;
                next(vm => {
                    data.forEach(item => {
                        item.loading = false;
                    });
                    vm.groups = data;
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
                        align: 'center',
                        key: 'show',
                        render(h, data) {
                            return h('checkbox', {
                                on: {
                                    input(value) {
                                        self.groups[data.index].show = value;
                                    },
                                    'on-change': value => {
                                        data.row.show = value;
                                        self.$loading.start();
                                        self.$notice.open({
                                            title: '正在更新信息分组信息...',
                                        });
                                        self.$http.post(`${window.api}/member/administration/information/group/edit`, data.row).then(() => {
                                            self.$loading.finish();
                                            self.$notice.open({
                                                title: '更新信息分组信息成功！',
                                            });
                                            self.refresh();
                                        }).catch(() => {
                                            self.$loading.fail();
                                            self.$notice.error({
                                                title: '更新信息分组信息失败！',
                                            });
                                        });
                                    },
                                },
                                props: {
                                    value: self.groups[data.index].show,
                                },
                            });
                        },
                        title: '显示',
                        width: 100,
                    },
                    {
                        key: 'order',
                        render(h, data) {
                            const store = data.row;
                            return h('i-input', {
                                on: {
                                    'on-change': event => {
                                        store.order = event.target.value;
                                    },
                                    'on-blur': () => {
                                        self.$loading.start();
                                        self.$notice.open({
                                            title: '正在更新信息分组信息...',
                                        });
                                        self.$http.post(`${window.api}/member/administration/information/group/edit`, store).then(() => {
                                            self.$loading.finish();
                                            self.$notice.open({
                                                title: '更新信息分组信息成功！',
                                            });
                                            self.refresh();
                                        }).catch(() => {
                                            self.$loading.fail();
                                            self.$notice.error({
                                                title: '更新信息分组信息失败！',
                                            });
                                        });
                                    },
                                },
                                props: {
                                    value: self.groups[data.index].order,
                                },
                            });
                        },
                        title: '显示顺序',
                        width: 200,
                    },
                    {
                        key: 'name',
                        title: '栏目分组名称',
                    },
                    {
                        key: 'handle',
                        render(h, data) {
                            let text;
                            if (self.groups[data.index].loading) {
                                text = injection.trans('content.global.delete.loading');
                            } else {
                                text = injection.trans('content.global.delete.submit');
                            }
                            return h('div', [
                                h('router-link', {
                                    props: {
                                        to: `/member/information/group/${data.row.id}/edit`,
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
                groups: [],
                loading: false,
                pagination: {},
                self: this,
            };
        },
        methods: {
            remove(index) {
                const self = this;
                const group = self.groups[index];
                group.loading = true;
                self.$http.post(`${window.api}/member/administration/information/group/remove`, {
                    id: group.id,
                }).then(() => {
                    self.$loading.finish();
                    self.$notice.open({
                        title: '删除信息分组成功！',
                    });
                    self.refresh();
                }).finally(() => {
                    group.loading = false;
                });
            },
            refresh() {
                const self = this;
                self.$notice.open({
                    title: '正在刷新数据...',
                });
                self.$loading.start();
                self.$http.post(`${window.api}/member/administration/information/group/list`, {
                    order: 'asc',
                    sort: 'order',
                }).then(response => {
                    const { data, pagination } = response.data;
                    data.forEach(item => {
                        item.loading = false;
                    });
                    self.$loading.finish();
                    self.$notice.open({
                        title: '刷新数据成功！',
                    });
                    self.groups = data;
                    self.pagination = pagination;
                }).catch(() => {
                    self.$loading.error();
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
                    <span class="text">信息分组</span>
                    <router-link class="extend" to="/member/information/group/create">
                        <i-button type="default">添加信息分组</i-button>
                    </router-link>
                </template>
                <div class="extend-info">
                    <p>用户数据分组列表</p>
                </div>
                <i-table :columns="columns" :context="self" :data="groups"></i-table>
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