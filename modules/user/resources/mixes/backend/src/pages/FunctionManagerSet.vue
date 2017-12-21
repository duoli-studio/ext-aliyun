<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(vm => {
                vm.parent.tab_name = to.query.tab_name;
                vm.parent.fun_name = to.query.fun_name;
            });
        },
        data() {
            return {
                functionList: [
                    {
                        title: '商品管理',
                        selected: false,
                        list: [
                            {
                                id: '1',
                                name: '商品添加',
                                selected: false,
                            },
                            {
                                id: '2',
                                name: '商品编辑',
                                selected: false,
                            },
                            {
                                id: '3',
                                name: '添加分类',
                                selected: false,
                            },
                        ],
                    },
                    {
                        title: '订单管理',
                        selected: false,
                        list: [
                            {
                                id: '10',
                                name: '编辑订单',
                                selected: false,
                            },
                            {
                                id: '11',
                                name: '编辑付款状态',
                                selected: false,
                            },
                            {
                                id: '12',
                                name: '编辑发货状态',
                                selected: false,
                            },
                        ],
                    },
                ],
                parent: {
                    fun_name: '',
                    tab_name: '',
                },
            };
        },
        methods: {
            checkAllGroupChange(wrap) {
                wrap.selected = true;
                wrap.list.forEach(item => {
                    if (item.selected === false) {
                        wrap.selected = false;
                    }
                });
            },
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
            handleCheckAll(wrap) {
                if (wrap.selected) {
                    wrap.list.forEach(item => {
                        item.selected = true;
                    });
                } else {
                    wrap.list.forEach(item => {
                        item.selected = false;
                    });
                }
            },
        },
    };
</script>
<template>
    <div class="member-warp">
        <div class="function-manager-set">
            <div class="return-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>设置"{{ parent.tab_name}}-{{ parent.fun_name }}"功能</span>
            </div>
            <card :bordered="false">
                <div class="function-module" v-for="wrap in functionList">
                    <div>
                        <checkbox
                                v-model="wrap.selected"
                                @on-change="handleCheckAll(wrap)">{{ wrap.title }}</checkbox>
                    </div>
                    <div>
                        <checkbox @on-change="checkAllGroupChange(wrap)"
                                 v-model="item.selected"
                                 v-for="item in wrap.list">{{ item.name }}</checkbox>
                    </div>
                </div>
            </card>
        </div>
    </div>
</template>