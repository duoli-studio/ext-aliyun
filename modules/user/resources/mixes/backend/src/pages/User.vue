<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/member/administration/user/list`, {
                format: 'beauty',
                with: [
                    'ban',
                    'groups',
                ],
            }).then(response => {
                const { data, pagination } = response.data.data;
                next(vm => {
                    data.forEach(item => {
                        if (item.ban) {
                            item.ban = item.ban.type;
                        } else {
                            item.ban = 0;
                        }
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
                        align: 'center',
                        type: 'selection',
                        width: 60,
                    },
                    {
                        key: 'avatar',
                        render(h, data) {
                            if (data.row.avatar) {
                                return h('img', {
                                    domProps: {
                                        class: {
                                            'user-list-image': true,
                                        },
                                        src: data.row.avatar,
                                    },
                                });
                            }
                            return '';
                        },
                        title: injection.trans('member.user.table.avatar'),
                        width: 66,
                    },
                    {
                        key: 'name',
                        title: injection.trans('member.user.table.title'),
                        width: 100,
                    },
                    {
                        key: 'nickname',
                        title: injection.trans('member.user.table.nickname'),
                        width: 100,
                    },
                    {
                        key: 'realname',
                        title: injection.trans('member.user.table.realname'),
                        width: 100,
                    },
                    {
                        key: 'email',
                        title: injection.trans('member.user.table.email'),
                    },
                    {
                        key: 'status',
                        render(h, data) {
                            if (data.row.ban === 0) {
                                return '不封禁';
                            } else if (data.row.ban > 0 && data.row.ban < 3) {
                                return '部分封禁';
                            }
                            return '完全封禁';
                        },
                        title: injection.trans('member.user.table.status'),
                        width: 100,
                    },
                    {
                        key: 'group',
                        title: injection.trans('member.user.table.group'),
                        width: 100,
                    },
                    {
                        key: 'created_at',
                        title: injection.trans('member.user.table.date'),
                        width: 150,
                    },
                    {
                        key: 'handle',
                        render(h, data) {
                            return h('div', [
                                h('router-link', {
                                    props: {
                                        to: `/member/user/${data.row.id}/group`,
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
                                    }, '用户组'),
                                ]),
                                h('router-link', {
                                    props: {
                                        to: `/member/user/${data.row.id}/tag`,
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
                                    }, '标签'),
                                ]),
                                h('router-link', {
                                    props: {
                                        to: `/member/user/${data.row.id}/edit`,
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
                                    }, '编辑详情'),
                                ]),
                                h('router-link', {
                                    props: {
                                        to: `/member/user/${data.row.id}/ban`,
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
                                    }, '封禁'),
                                ]),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.remove(data.row.id);
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'error',
                                    },
                                    style: {
                                        marginLeft: '10px',
                                    },
                                }, '删除'),
                            ]);
                        },
                        title: injection.trans('member.user.table.handle'),
                        width: 360,
                    },
                ],
                groups: [],
                keyword: '',
                list: [],
                modal: {
                    loading: true,
                    visible: false,
                },
                pagination: {},
                selections: [],
                self: this,
                trans: injection.trans,
                user: {},
            };
        },
        methods: {
            changePage(page) {
                const self = this;
                self.$loading.start();
                self.$notice.open({
                    title: '正在搜索数据...',
                });
                self.$http.post(`${window.api}/member/administration/user/list?page=${page}`, {
                    format: 'beauty',
                    with: [
                        'ban',
                        'groups',
                    ],
                }).then(response => {
                    if (response.data.data.length > 0) {
                        self.list = response.data.data.map(item => {
                            if (item.ban) {
                                item.ban = item.ban.type;
                            } else {
                                item.ban = 0;
                            }
                            return item;
                        });
                    }
                    self.pagination = response.data.pagination;
                    injection.loading.finish();
                    self.$notice.open({
                        title: '搜索数据完成！',
                    });
                });
            },
            output() {
                window.console.log('Output done!');
            },
            remove(index) {
                this.user = this.list[index];
                this.modal.visible = true;
            },
            search() {
                const self = this;
                if (self.keyword === '') {
                    self.$notice.error({
                        title: '请输入搜索关键词！',
                    });
                    return false;
                }
                self.$http.post(`${window.api}/member/administration/user/list`, {
                    format: 'beauty',
                    search: self.keyword,
                    with: [
                        'ban',
                        'groups',
                    ],
                }).then(response => {
                    const { data, pagination } = response.data.data;
                    data.forEach(item => {
                        if (item.ban) {
                            item.ban = item.ban.type;
                        } else {
                            item.ban = 0;
                        }
                    });
                    self.list = data;
                    self.pagination = pagination;
                    injection.loading.finish();
                }).catch(() => {
                    self.$loading.error();
                });
                return true;
            },
            selection(items) {
                this.selections = items;
            },
            tag(id) {
                this.$router.push(`/member/user/${id}/tag`);
            },
        },
        mounted() {
            this.$store.commit('title', '用户管理 - 用户中心');
        },
    };
</script>
<template>
    <div class="member-warp">
        <div class="user-list">
            <card :bordered="false">
                <template slot="title">
                    <span class="text">用户列表</span>
                    <div class="search">
                        <router-link to="/member/user/create">
                            <i-button type="default">添加用户</i-button>
                        </router-link>
                        <!--<i-button type="default" @click.native="output">导出数据</i-button>-->
                        <i-input :placeholder="trans('content.global.search.placeholder')" v-model="keyword">
                            <!--<i-select v-model="select3" slot="prepend" style="width: 80px">-->
                                <!--<Option value="day">日活</Option>-->
                                <!--<Option value="month">月活</Option>-->
                            <!--</i-select>-->
                            <i-button slot="append" icon="ios-search" @click.native="search"></i-button>
                        </i-input>
                    </div>
                </template>
                <i-table :columns="columns" :context="self" :data="list" @on-selection-change="selection"></i-table>
                <div class="ivu-page-wrap">
                    <page :current="pagination.current"
                          :page-size="pagination.paginate"
                          :total="pagination.count"
                          @on-change="changePage"></page>
                </div>
                <modal class-name="user-list-modal"
                       :loading="modal.loading"
                       :mask-closable="false"
                       title="删除用户"
                       :value="modal.visible"
                       :width="772"
                       @on-cancel="modal.visible = false"
                       @on-ok="remove">
                    <p>本操作不可恢复，您确定要删除用户 <strong>{{ user.name }}</strong> 吗？</p>
                    <i-form label-position="right" :label-width="148" :model="user" ref="form">
                        <form-item label="删除用户的理由">
                            <i-input type="textarea" placeholder="请输入删除用户的理由" v-model="user.reason"
                                     :autosize="{minRows: 5,maxRows: 9}"></i-input>
                            <p class="info">请输入操作理由，系统将把理由记录在用户删除记录中，以供日后查看。</p>
                        </form-item>
                    </i-form>
                </modal>
            </card>
        </div>
    </div>
</template>