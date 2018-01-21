<script>
    import injection, {trans} from '../helpers/injection';
    // import ISelect from "iview/src/components/select/select";
    export default {
        // name : 'region'
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/g/backend`, {
                query         : `
                query servers ($pagination: InputPagination!){
                  servers : servers(pagination:$pagination){
                    list{
                      id
                      code
                      title
                      parent_id
                      is_enable
                      is_default
                    }
                  }
                }
                `,
                variables     : {
                    pagination : {
                    }
                },
                operationName : 'roles'
            }).then(response => {
                const {data} = response.data;
                next(vm => {
                    data.servers.forEach(item => {
                        console.log(item);
                    });
                    vm.roles_data = data.servers;
                    injection.loading.finish();
                });
                console.log('=========roles==========');
                console.log(data.roles);
            }).catch(() => {
                injection.loading.error();
            });
        },
        mounted() {
            this.$store.commit('title', trans('游戏区服管理列表region'));
        },
        data() {
            // const that = this;
            return {
                roles_column : [
                    {
                        title : '游戏ID',
                        key   : 'id'
                    },
                    {
                        title : '游戏代码',
                        key   : 'name'
                    },
                    {
                        title : '区服名称(树形级别)',
                        key   : 'title'
                    },
                    {
                        title : '操作',
                        key   : 'handle',
                        align : 'center',
                        // render(h, params) {
                        //     return h('div', [
                        //         h('i-button', {
                        //             props : {
                        //                 type : 'warning',
                        //                 size : 'small'
                        //             },
                        //             style : {
                        //                 marginRight : '5px'
                        //             },
                        //             on    : {
                        //                 click : () => {
                        //                     that.show(params);
                        //                 }
                        //             }
                        //         }, '权限'),
                        //         h('i-button', {
                        //             props : {
                        //                 type : 'primary',
                        //                 size : 'small'
                        //             },
                        //             style : {
                        //                 marginRight : '5px'
                        //             },
                        //             on    : {
                        //                 click : () => {
                        //                     that.UpdateRole(params);
                        //                 }
                        //             }
                        //         }, '编辑'),
                        //         h('poptip', {
                        //             props : {
                        //                 confirm   : true,
                        //                 width     : 200,
                        //                 title     : '确认删除此角色 ? ',
                        //                 placement : 'left'
                        //             },
                        //             on    : {
                        //                 'on-ok' : () => {
                        //                     that.remove(params);
                        //                 }
                        //             }
                        //         }, [
                        //             h('i-button', {
                        //                 props : {
                        //                     type : 'error',
                        //                     size : 'small'
                        //                 },
                        //             }, '删除')
                        //         ])
                        //     ]);
                        // },
                    },
                ],
            };
        },
        methods : {
        }
    };
</script>

<template>
    <div class="page-wrap">
        <div class="return-link-title tab-pane-title"
             style="color: #328cf1;background-color: #ffffff;font-size: 23px;padding:15px;margin-bottom: 10px;">
            <span>游戏区服管理列表</span>
        </div>
        <div style="background-color: #ffffff">
            <i-button class="btn-action" type="success"
                      @click.native="createOrganization" style="margin-bottom:8px;margin-top: 8px;margin-left: 8px">╋新增
            </i-button>
            <i-input :rows="2" style="width: 200px;float: right;margin-bottom:8px;margin-top: 8px"
                     placeholder="搜索..." icon="ios-search" on-click="search()" v-model="searchValue"></i-input>
            <i-table :columns="roles_column" :data="roles_data"></i-table>
        </div>
    </div>
</template>


<style scoped>

</style>