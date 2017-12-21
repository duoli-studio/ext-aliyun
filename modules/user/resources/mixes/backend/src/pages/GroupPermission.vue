<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/member/administration/permission/get`).then(response => {
                window.console.log(response);
                next(vm => {
                    vm.groups = response.data.data.groups;
                    vm.modules = response.data.data.modules;
                    vm.permissions = response.data.data.permissions;
                    vm.types = response.data.data.types;
                    injection.loading.finish();
                });
            }).catch(() => {
                injection.loading.error();
            });
        },
        data() {
            return {
                groups: [],
                open: 'global',
                permissions: [],
                modules: [],
                types: [],
            };
        },
        methods: {
            change() {
                const self = this;
                self.$loading.start();
                self.$notice.open({
                    desc: '等待期间请不要继续操作！',
                    title: '正在批量更新权限值，请耐心等待...',
                });
                self.$http.post(`${window.api}/member/administration/permission/set`, {
                    data: self.types,
                }).then(() => {
                    self.$loading.finish();
                    self.$notice.open({
                        desc: '可以继续操作了...',
                        title: '批量更新权限值成功！',
                    });
                }).catch(() => {
                    self.$loading.error();
                });
            },
        },
    };
</script>
<template>
    <div class="member-warp">
        <div class="group-permission">
            <tabs value="global">
                <tab-pane :label="module.attributes.name" :name="module.attributes.identification" v-for="(module, key) in modules">
                    <card :bordered="false">
                        <ul class="types">
                            <li v-for="type in types">{{ type.attributes.name }}</li>
                        </ul>
                        <div class="groups" v-for="group in module.groups">
                            <div class="header">{{ group.attributes.name }}</div>
                            <ul v-for="permission in group.permissions">
                                <li>{{ permission.description }}</li>
                                <li v-for="(type, index) in types">
                                    <i-select multiple
                                              :title="type.attributes.identification + '::' + module.attributes.identification + '::' + group.attributes.identification + '::' + permission.identification"
                                              v-model="types[index].has[type.attributes.identification + '::' + module.attributes.identification + '::' + group.attributes.identification + '::' + permission.identification]"
                                              @on-change="change">
                                        <i-option :key="g" :value="g.identification" v-for="g in groups">{{ g.name }}</i-option>
                                    </i-select>
                                </li>
                            </ul>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>