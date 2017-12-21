<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/member/administration/tag/list`).then(response => {
                window.console.log(response);
                next(vm => {
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
                        type: 'selection',
                        width: 100,
                    },
                    {
                        key: 'tag',
                        title: injection.trans('标签名称'),
                        width: 200,
                    },
                    {
                        key: 'users',
                        title: injection.trans('用户数'),
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
                                text = injection.trans('content.global.delete.loading');
                            } else {
                                text = injection.trans('content.global.delete.submit');
                            }
                            return h('i-button', {
                                on: {
                                    click() {
                                        self.remove(data.index);
                                    },
                                },
                                props: {
                                    size: 'small',
                                    type: 'error',
                                },
                            }, [
                                h('span', text),
                            ]);
                        },
                        title: injection.trans('member.user.table.handle'),
                        width: 300,
                    },
                ],
                form: {
                    tags: [],
                    target: '',
                    type: 'delete',
                },
                types: [
                    {
                        label: 'delete',
                        text: '删除',
                    },
                ],
                list: [],
                loading: false,
                self: this,
            };
        },
        methods: {
            selection(items) {
                const self = this;
                self.form.tags = [];
                items.forEach(item => {
                    self.form.tags.push(item.id);
                });
            },
            submit() {
                const self = this;
                self.loading = true;
                if (!self.form.tags.length) {
                    self.$notice.error({
                        title: '请先选择标签！',
                    });
                    self.loading = false;
                }
                if (self.form.type === 'combine' && self.form.target === '') {
                    self.$notice.error({
                        title: '请输入合并到的标签名称！',
                    });
                    self.loading = false;
                } else {
                    self.$http.post(`${window.api}/member/administration/tag/patch`, self.form).then(() => {
                        self.$notice.open({
                            title: '批量更新标签数据成功！',
                        });
                        self.$notice.open({
                            title: '准备更新标签数据...',
                        });
                        self.$loading.start();
                        self.$http.post(`${window.api}/member/administration/tag/list`).then(response => {
                            self.list = response.data.data;
                            self.$loading.finish();
                        }).catch(() => {
                            self.$loading.error();
                        });
                    }).finally(() => {
                        self.loading = false;
                    });
                }
            },
        },
    };
</script>
<template>
    <div class="member-warp">
        <div class="user-tag">
            <card :bordered="false">
                <template slot="title">
                    <span class="text">用户标签</span>
                    <router-link class="extend" to="/member/tag/create">
                        <i-button type="default">添加用户标签</i-button>
                    </router-link>
                </template>
                <i-table :columns="columns" :context="self" :data="list" @on-selection-change="selection"></i-table>
                <i-form :label-width="0" :model="form">
                    <row>
                        <i-col span="24">
                            <form-item label="批量操作">
                                <radio-group v-model="form.type">
                                    <radio :label="item.label" v-for="item in types">
                                        <span>{{ item.text }}</span>
                                    </radio>
                                </radio-group>
                                <!--<i-input placeholder="请输入标签名称"-->
                                         <!--size="small"-->
                                         <!--v-if="form.type === 'combine'"-->
                                         <!--v-model="form.target">-->
                                <!--</i-input>-->
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item>
                                <i-button :loading="loading" type="primary" @click.native="submit">
                                    <span v-if="!loading">确认提交</span>
                                    <span v-else>正在提交…</span>
                                </i-button>
                            </form-item>
                        </i-col>
                    </row>
                </i-form>
            </card>
        </div>
    </div>
</template>