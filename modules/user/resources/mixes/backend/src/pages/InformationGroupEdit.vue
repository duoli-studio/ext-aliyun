<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/member/administration/information/group`, {
                id: to.params.id,
            }).then(response => {
                const { data, informations } = response.data.data;
                next(vm => {
                    vm.form = data;
                    vm.form.informations = [];
                    Object.keys(informations).forEach(index => {
                        informations[index].label = informations[index].id;
                        informations[index].text = informations[index].name;
                        if (informations[index].exists) {
                            vm.form.informations.push(informations[index].id);
                        }
                    });
                    window.console.log(vm.form.informations);
                    window.console.log(vm.informations);
                    vm.informations = informations;
                    injection.loading.finish();
                });
            }).catch(() => {
                injection.loading.fail();
            });
        },
        data() {
            return {
                form: {
                    id: 1,
                    informations: [],
                    name: '特别信息',
                    order: '1',
                    show: false,
                },
                informations: [],
                loading: false,
            };
        },
        methods: {
            submit() {
                const self = this;
                self.loading = true;
                self.$http.post(`${window.api}/member/administration/information/group/edit`, self.form).then(() => {
                    self.$notice.open({
                        title: '编辑信息分组信息成功！',
                    });
                    self.$router.push('/member/information/group');
                }).catch(() => {
                    self.$notice.error({
                        title: '编辑信息分组信息失败！',
                    });
                }).finally(() => {
                    self.loading = false;
                });
            },
        },
    };
</script>
<template>
    <div class="member-warp">
        <div class="user-information-create">
            <card :bordered="false">
                <p slot="title">编辑信息分组</p>
                <i-form :label-width="200" :model="form" ref="form" :rules="rules">
                    <row>
                        <i-col span="12">
                            <form-item label="分组名称" prop="name">
                                <i-input placeholder="请输入分组名称" v-model="form.name"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="前台显示">
                                <i-switch v-model="form.show" size="large">
                                    <span slot="open">开启</span>
                                    <span slot="close">关闭</span>
                                </i-switch>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="显示顺序">
                                <i-input placeholder="请输入显示顺序" v-model="form.order"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="用户资料项">
                                <checkbox-group v-model="form.informations">
                                    <checkbox :label="item.label" style="width: 31%" v-for="item in informations">
                                        <span>{{ item.text }}</span>
                                    </checkbox>
                                </checkbox-group>
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