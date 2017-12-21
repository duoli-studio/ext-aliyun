<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(vm => {
                vm.parent.id = to.query.id;
            });
        },
        data() {
            return {
                form: {
                    end: '',
                    id: '',
                    name: 'wrwe',
                    now: 0,
                    reason: '',
                    time: 0,
                    type: 0,
                },
                loading: false,
                parent: {
                    id: '',
                },
                rules: {},
                times: [
                    {
                        label: '永久',
                        value: 0,
                    },
                    {
                        label: '一天',
                        value: 1,
                    },
                    {
                        label: '一周',
                        value: 2,
                    },
                    {
                        label: '一个月',
                        value: 3,
                    },
                    {
                        label: '一年',
                        value: 4,
                    },
                    {
                        label: '其他时间',
                        value: 5,
                    },
                ],
            };
        },
        methods: {
            dateChange(date) {
                this.form.end = date;
            },
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
            submit() {
                const self = this;
                self.$router.push('/member/user/manager');
            },
        },
    };
</script>
<template>
    <div class="member-warp">
        <div class="user-manager-ban">
            <div class="return-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>用户管理-封禁</span>
            </div>
            <card :bordered="false">
                <i-form :label-width="200" :model="form" ref="form" :rules="rules">
                    <row>
                        <i-col span="12">
                            <form-item label="禁止用户名">
                                <span>{{ form.name }}</span>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="当前状态">
                                <span v-if="form.now === 0">不封禁</span>
                                <span v-if="form.now > 0 && form.now < 3">部分封禁</span>
                                <span v-if="form.now > 2">完全封禁</span>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="禁止类型">
                                <radio-group v-model="form.type" size="large" vertical>
                                    <radio label="0">
                                        <span>不封禁</span>
                                    </radio>
                                    <radio label="1">
                                        <span>可浏览网站开放内容</span>
                                    </radio>
                                    <radio label="2">
                                        <span>可浏览个人资料</span>
                                    </radio>
                                    <radio label="3">
                                        <span>禁止登陆</span>
                                    </radio>
                                </radio-group>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="8">
                            <form-item label="封禁时间">
                                <i-select v-model="form.time">
                                    <i-option v-for="item in times"
                                              :value="item.value"
                                              :key="item">{{ item.label }}</i-option>
                                </i-select>
                                <date-picker :placeholder="请选择封禁截止时间"
                                             style="margin-top: 20px;"
                                             type="date"
                                             :value="form.end"
                                             v-if="form.time === 5"
                                             @on-change="dateChange">
                                </date-picker>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="禁止/解禁用户的理由" prop="users">
                                <i-input :autosize="{minRows: 5,maxRows: 9}"
                                         placeholder="请输入禁止/解禁用户的理由"
                                         type="textarea"
                                         v-model="form.users"></i-input>
                                <p class="tip">请输入操作理由，系统将把理由记录在用户禁止记录中，以供日后查看。</p>
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