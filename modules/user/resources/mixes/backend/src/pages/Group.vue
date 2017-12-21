<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/member/administration/group/list`).then(response => {
                next(vm => {
                    response.data.data.forEach(item => {
                        item.loading = false;
                    });
                    vm.list = response.data.data;
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
                        type: 'selection',
                        width: 60,
                    },
                    {
                        key: 'icon',
                        render(h, data) {
                            if (data.row.icon) {
                                return h('img', {
                                    domProps: {
                                        class: {
                                            'group-list-image': true,
                                        },
                                        src: data.row.icon,
                                    },
                                });
                            }
                            return '';
                        },
                        title: injection.trans('用户组图标'),
                        width: 100,
                    },
                    {
                        key: 'name',
                        title: injection.trans('用户组名称'),
                        width: 200,
                    },
                    {
                        key: 'status',
                        title: injection.trans('当前已有模块'),
                        width: 200,
                    },
                    {
                        align: 'right',
                        key: 'members_count',
                        title: injection.trans('已有用户数'),
                        width: 100,
                    },
                    {
                        key: 'created_at',
                        title: injection.trans('创建时间'),
                    },
                    {
                        key: 'handle',
                        render(h, data) {
                            let text;
                            if (self.list[data.index].loading) {
                                text = injection.trans('正在删除...');
                            } else {
                                text = injection.trans('删除');
                            }
                            return h('div', [
                                h('router-link', {
                                    props: {
                                        to: `/member/group/${data.row.id}/combine`,
                                    },
                                    style: {
                                        marginLeft: '10px',
                                    },
                                }, [
                                    h('i-button', {
                                        props: {
                                            size: 'small',
                                            type: 'default',
                                        },
                                    }, '合并用户组'),
                                ]),
                                h('router-link', {
                                    props: {
                                        to: `/member/group/${data.row.id}/edit`,
                                    },
                                    style: {
                                        marginLeft: '10px',
                                    },
                                }, [
                                    h('i-button', {
                                        props: {
                                            size: 'small',
                                            type: 'default',
                                        },
                                    }, '编辑用户组'),
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
                        title: injection.trans('member.user.table.handle'),
                        width: 300,
                    },
                ],
                groups: [],
                keyword: '',
                list: [],
                pagination: {
                    last_page: 1,
                },
                selections: [],
                self: this,
                trans: injection.trans,
            };
        },
        methods: {
            remove(index) {
                const self = this;
                const group = self.list[index];
                window.console.log(group);
                group.loading = true;
                self.$http.post(`${window.api}/member/administration/group/remove`, {
                    id: group.id,
                }).then(() => {
                    self.$notice.open({
                        title: '删除用户组成功，正在刷新数据...',
                    });
                    self.$loading.start();
                    self.$http.post(`${window.api}/member/administration/group/list`).then(response => {
                        self.$loading.finish();
                        self.list = response.data.data;
                        self.list.forEach(item => {
                            item.loading = false;
                        });
                    }).catch(() => {
                        self.$loading.error();
                    });
                }).catch(() => {
                    group.loading = false;
                });
            },
            selection(items) {
                this.selections = items;
            },
        },
        mounted() {
            this.$store.commit('title', '用户组管理 - 用户中心');
        },
    };
</script>
<template>
    <div class="member-warp">
        <div class="group-list">
            <card :bordered="false">
                <template slot="title">
                    <span class="text">用户组列表</span>
                    <router-link class="extend" to="/member/group/create">
                        <i-button type="default">添加用户组</i-button>
                    </router-link>
                </template>
                <i-table :columns="columns" :context="self" :data="list" @on-selection-change="selection"></i-table>
            </card>
        </div>
    </div>
</template>