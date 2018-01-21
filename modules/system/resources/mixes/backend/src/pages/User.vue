<template>
    <div class="page-wrap">
        <div class="return-link-title tab-pane-title"
             style="color: #328cf1;background-color: #ffffff;font-size: 23px;padding:15px;margin-bottom: 10px;">
            <span>用户管理列表</span>
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
            this.$store.commit('title', trans('用户管理列表User'));
        },
        data() {
            // const that = this;
            return {
                roles_column : [
                    {
                        title : 'ID',
                        key   : 'id'
                    },
                    {
                        title : '用户名',
                        key   : 'name'
                    },
                    {
                        title : '手机号',
                        key   : 'title'
                    },
                    {
                        title : '操作',
                        key   : 'handle',
                        align : 'center',
                    },
                ],
            };
        },
        methods : {
        }
    };
</script>

<style scoped>

</style>