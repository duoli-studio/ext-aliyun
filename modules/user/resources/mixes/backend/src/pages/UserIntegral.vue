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
                        key: 'section',
                        title: '用户组积分段',
                        width: 150,
                    },
                    {
                        key: 'quantity',
                        title: '积分值',
                        width: 150,
                    },
                    {
                        key: 'one',
                        render(h, data) {
                            return h('i-input', {
                                on: {
                                    input(value) {
                                        self.list[data.index].one = value;
                                    },
                                },
                                props: {
                                    value: self.list[data.index].one,
                                },
                            });
                        },
                        title: '威望',
                        width: 150,
                    },
                    {
                        key: 'two',
                        render(h, data) {
                            return h('i-input', {
                                on: {
                                    input(value) {
                                        self.list[data.index].two = value;
                                    },
                                },
                                props: {
                                    value: self.list[data.index].two,
                                },
                            });
                        },
                        title: '金钱',
                        width: 150,
                    },
                    {
                        key: 'three',
                        render(h, data) {
                            return h('i-input', {
                                on: {
                                    input(value) {
                                        self.list[data.index].three = value;
                                    },
                                },
                                props: {
                                    value: self.list[data.index].three,
                                },
                            });
                        },
                        title: '其他',
                        width: 150,
                    },
                    {
                        key: 'none',
                        title: '',
                    },
                ],
                integral: [
                    {
                        one: '100',
                        quantity: 100,
                        section: '0~99',
                        three: '100',
                        two: '100',
                    },
                ],
                form: {
                    reason: '',
                },
                loading: false,
                rules: {},
                self: this,
            };
        },
    };
</script>
<template>
    <div class="member-warp">
        <div class="user-create">
            <card :bordered="false">
                <p slot="title">编辑用户积分</p>
                <div class="extend-info">
                    <p>提示</p>
                    <p>修改用户的某项积分会造成该用户总积分的变化，从引起普通会员等级的变化，因此请仔细设置各项积分。</p>
                </div>
                <i-form :label-width="200" :model="form" ref="form" :rules="rules">
                    <i-table :columns="columns" :context="self" :data="integral"></i-table>
                    <p class="extend-title">变更理由</p>
                    <row>
                        <i-col span="12">
                            <form-item label="变更用户组的理由" prop="users">
                                <i-input :autosize="{minRows: 5,maxRows: 9}"
                                         placeholder="请输入变更用户组的理由"
                                         type="textarea"
                                         v-model="form.reason">
                                </i-input>
                                <p class="info">如果您通过用户组设定禁止或解除禁止该用户，请输入操作理由，系统将把理由记录在用户禁止记录中，以供日后查看。</p>
                            </form-item>
                        </i-col>
                    </row>
                </i-form>
            </card>
        </div>
    </div>
</template>