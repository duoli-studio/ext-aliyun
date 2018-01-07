<script>
	export default {
		methods : {
			expand(index) {
				this.originals[index].expand = !this.originals[index].expand;
			},
		},
		name    : 'menu-children',
		props   : {
			colspan   : {
				default : 0,
				type    : Number,
			},
			items     : {
				default : {},
				type    : Object,
			},
			originals : {
				default : {},
				type    : Object,
			},
			rules     : {
				default : {},
				type    : Object,
			},
		},
	};
</script>
<template>
    <tr v-if="Object.keys(items).length > 0">
        <td :colspan="colspan">
            <div style="margin-left: 100px; margin-top: -1px; margin-bottom: -1px;">
                <div class="ivu-table-wrapper">
                    <div class="ivu-table">
                        <div class="ivu-table-body">
                            <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                <colgroup>
                                    <col width="60">
                                    <col width="200">
                                    <col width="400">
                                    <col>
                                    <col width="200">
                                </colgroup>
                                <tbody class="ivu-table-tbody">
                                <template v-for="(item, index) in items">
                                    <tr class="ivu-table-row">
                                        <td>
                                            <div class="ivu-table-cell ivu-table-cell-with-expand"
                                                 v-if="Object.keys(item.children).length > 0"
                                                 @click="expand(item.index)">
                                                <template v-if="originals[item.index].expand">
                                                    <div class="ivu-table-cell-expand ivu-table-cell-expand-expanded">
                                                        <div class="ivu-table-cell-expand">
                                                            <i class="ivu-icon ivu-icon-ios-arrow-right"></i>
                                                        </div>
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <div class="ivu-table-cell-expand">
                                                        <div class="ivu-table-cell-expand">
                                                            <i class="ivu-icon ivu-icon-ios-arrow-right"></i>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="ivu-table-cell">
                                                <form-item label=""
                                                           :prop="item.index + '.order'"
                                                           :rules="rules.order"
                                                           style="margin-bottom: 0;">
                                                    <i-input :number="true" placeholder="请输入规则排序"
                                                             v-model="originals[item.index].order"></i-input>
                                                </form-item>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="ivu-table-cell">
                                                <form-item label=""
                                                           :prop="item.index + '.text'"
                                                           :rules="rules.text"
                                                           style="margin-bottom: 0;">
                                                    <i-input placeholder="请输入菜单名称"
                                                             v-model="originals[item.index].text"></i-input>
                                                </form-item>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td>
                                            <div class="ivu-table-cell">
                                                <form-item label=""
                                                           :prop="item.index + '.text'"
                                                           :rules="rules.text"
                                                           style="margin-bottom: 0;">
                                                    <i-switch v-model="originals[item.index].enabled"></i-switch>
                                                </form-item>
                                            </div>
                                        </td>
                                    </tr>
                                    <menu-children :colspan="colspan"
                                                   :items="item.children"
                                                   :originals="originals"
                                                   :rules="rules"
                                                   v-if="Object.keys(item.children).length > 0 && originals[item.index].expand"></menu-children>
                                </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
</template>