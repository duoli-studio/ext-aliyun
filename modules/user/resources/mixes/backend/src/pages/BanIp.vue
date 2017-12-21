<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/member/administration/ban/ip`).then(response => {
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
                        key: 'ip',
                        title: injection.trans('IP 地址'),
                        width: 300,
                    },
                    {
                        key: 'end',
                        title: injection.trans('封禁截止时间'),
                        width: 200,
                    },
                    {
                        key: 'reason',
                        title: injection.trans('封禁原因'),
                    },
                    {
                        key: 'created_at',
                        title: injection.trans('操作时间'),
                        width: 200,
                    },
                    {
                        key: 'handle',
                        render(h, data) {
                            let text;
                            if (self.list[data.index].loading) {
                                text = '删除 IP';
                            } else {
                                text = '正在删除 IP…';
                            }
                            return h('i-button', {
                                on: {
                                    click() {
                                        self.remove(data.index);
                                    },
                                },
                                props: {
                                    loading: self.list[data.index].loading,
                                    size: 'small',
                                    type: 'error',
                                },
                            }, [
                                h('span', text),
                            ]);
                        },
                        title: injection.trans('操作'),
                        width: 300,
                    },
                ],
                list: [],
                pagination: {},
                self: this,
            };
        },
        methods: {
            paginator(page) {
                const self = this;
                if (page < 1) {
                    self.$notice.error({
                        title: '页码错误！',
                    });
                }
                self.$loading.start();
                self.$notice.open({
                    title: '正在更新数据',
                });
                self.$http.post(`${window.api}/member/administration/ban/ip`).then(response => {
                    const { data, pagination } = response.data;
                    data.forEach(item => {
                        item.loading = false;
                    });
                    self.$loading.finish();
                    self.list = data;
                    self.pagination = pagination;
                }).catch(() => {
                    self.$loading.error();
                });
            },
            remove(index) {
                const self = this;
                const item = self.list[index];
                item.loading = true;
                self.$http.post(`${window.api}/member/administration/ban/remove`, {
                    id: item.id,
                }).then(() => {
                    self.$notice.open({
                        title: '删除 IP 成功！',
                    });
                    self.paginator(1);
                }).catch(() => {
                    self.$notice.error({
                        title: '删除 IP 失败！',
                    });
                }).finally(() => {
                    item.loading = false;
                });
            },
        },
    };
</script>
<template>
    <div class="member-warp">
        <div class="user-ban">
            <card :bordered="false">
                <template slot="title">
                    <span class="text">封禁 IP</span>
                    <div class="search">
                        <router-link to="/member/ban/ip/create">
                            <i-button type="default">添加封禁 IP</i-button>
                        </router-link>
                    </div>
                </template>
                <i-table :columns="columns" :context="self" :data="list" @on-selection-change="selection"></i-table>
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