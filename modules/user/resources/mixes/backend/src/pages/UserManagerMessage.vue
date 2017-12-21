<script>
    export default {
        beforeRouteEnter(to, from, next) {
            if (to.query.type === '1') {
                next(vm => {
                    vm.parent.type = to.query.type;
                    vm.parent.name = to.query.name;
                });
            } else if (to.query.type === '0') {
                next(vm => {
                    vm.parent.type = to.query.type;
                });
            }
        },
        data() {
            const self = this;
            const reg = /^[0-9]*$/;
            const validatorSort = (rule, value, callback) => {
                if (!reg.test(value)) {
                    callback(new Error('权限值为数字'));
                } else {
                    callback();
                }
            };
            return {
                action: `${window.api}/zn/admin/upload`,
                columns: [
                    {
                        key: 'name',
                        title: '可选值名称',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        title: '操作',
                        width: 172,
                    },
                ],
                list: [],
                createModal: {
                    last_name: [],
                    name: '',
                    title: '',
                },
                deleteModal: {
                    authority: 0,
                    id: '',
                    name: '',
                },
                form: {
                    check_time: '',
                    check_value: '',
                    intro: '',
                    name: '',
                    sort: null,
                    status: true,
                    time_area: '',
                    type: 0,
                },
                loading: false,
                modal: false,
                modalCreate: false,
                parent: {
                    name: '',
                    type: '',
                },
                parentList: [
                    {
                        children: [
                            {
                                label: '部门1-1',
                                value: '11',
                            },
                            {
                                label: '部门1-2',
                                value: '12',
                            },
                        ],
                        label: '部门1',
                        value: '1',
                    },
                    {
                        children: [
                            {
                                children: [
                                    {
                                        label: '部门2-1-1',
                                        value: '211',
                                    },
                                ],
                                label: '部门2-1',
                                value: '21',
                            },
                            {
                                label: '部门2-2',
                                value: '22',
                            },
                        ],
                        label: '部门2',
                        value: '2',
                    },
                ],
                rules: {
                    check_time: [
                        {
                            message: '时间组件类型不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    check_value: [
                        {
                            message: '可选值不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    name: [
                        {
                            message: '名称不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    time_area: [
                        {
                            message: '时间组件类型不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    type: [
                        {
                            message: '类型不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    sort: [
                        {
                            trigger: 'blur',
                            validator: validatorSort,
                        },
                    ],
                },
                rulesModal: {
                    name: [
                        {
                            message: '可选值名称不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                },
                buttonProps: {
                    type: 'ghost',
                    size: 'small',
                },
                selectValueList: [
                    {
                        title: '父级parent 1',
                        expand: true,
                        render(h, { root, node, data }) {
                            return h('span', {
                                style: {
                                    display: 'inline-block',
                                    width: '100%',
                                },
                            }, [
                                h('span', [
                                    h('span', data.title),
                                    h('i-button', {
                                        props: {
                                            size: 'small',
                                        },
                                        style: {
                                            marginLeft: '10px',
                                        },
                                        on: {
                                            click() {},
                                        },
                                    }, '新增下级'),
                                ]),
                                h('span', {
                                    style: {
                                        display: 'inline-block',
                                        float: 'right',
                                        marginRight: '32px',
                                    },
                                }, [
                                    h('i-button', {
                                        props: {
                                            size: 'small',
                                            type: 'ghost',
                                        },
                                        style: {
                                            marginRight: '8px',
                                        },
                                        on: {
                                            click() {
                                                self.createModal.title = '编辑';
                                                self.createModal.name = data.title;
                                                self.modalCreate = true;
                                                window.console.log(root);
                                                window.console.log(node);
                                            },
                                        },
                                    }, '编辑'),
                                    h('i-button', {
                                        props: {
                                            size: 'small',
                                            type: 'ghost',
                                        },
                                        on: {
                                            click() {
                                                self.remove(root, node, data);
                                            },
                                        },
                                    }, '删除'),
                                ]),
                            ]);
                        },
                        children: [
                            {
                                title: 'child 1-1',
                                expand: true,
                                children: [
                                    {
                                        title: 'leaf 1-1-1',
                                        expand: true,
                                    },
                                    {
                                        title: 'leaf 1-1-2',
                                        expand: true,
                                    },
                                ],
                            },
                            {
                                title: 'child 1-2',
                                expand: true,
                                children: [
                                    {
                                        title: 'leaf 1-2-1',
                                        expand: true,
                                    },
                                    {
                                        title: 'leaf 1-2-1',
                                        expand: true,
                                    },
                                ],
                            },
                        ],
                    },
                ],
                timeTypes: [
                    {
                        label: '选日期及时间',
                        value: 0,
                    },
                    {
                        label: '选年月日',
                        value: 1,
                    },
                    {
                        label: '选年月',
                        value: 2,
                    },
                    {
                        label: '仅选年',
                        value: 3,
                    },
                    {
                        label: '仅选时间',
                        value: 4,
                    },
                ],
                timeTypesArea: [
                    {
                        label: '选日期及时间',
                        value: 0,
                    },
                    {
                        label: '选年月日',
                        value: 1,
                    },
                    {
                        label: '仅选时间',
                        value: 2,
                    },
                ],
                types: [
                    {
                        label: '请选择信息类型',
                        value: 0,
                    },
                    {
                        label: '单行文本框',
                        value: 1,
                    },
                    {
                        label: '多行文本框',
                        value: 2,
                    },
                    {
                        label: '单选框',
                        value: 3,
                    },
                    {
                        label: '多选框',
                        value: 4,
                    },
                    {
                        label: '复选框',
                        value: 5,
                    },
                    {
                        label: '日期时间选择',
                        value: 6,
                    },
                    {
                        label: '日期时间范围选择',
                        value: 7,
                    },
                    {
                        label: '下拉菜单',
                        value: 8,
                    },
                    {
                        label: '级联下拉菜单',
                        value: 9,
                    },
                    {
                        label: '上传图片',
                        value: 10,
                    },
                    {
                        label: '上传文件',
                        value: 11,
                    },
                ],
            };
        },
        methods: {
            addSelectValue() {
                this.createModal.title = '新增';
                this.modalCreate = true;
            },
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
            filterDepartment() {},
            submit() {},
            renderContent(h, { root, node, data }) {
                return h('span', {
                    style: {
                        display: 'inline-block',
                        width: '100%',
                    },
                }, [
                    h('span', [
                        h('span', data.title),
                        h('i-button', {
                            props: {
                                size: 'small',
                            },
                            style: {
                                marginLeft: '10px',
                            },
                            on: {
                                click() {},
                            },
                        }, '新增下级'),
                    ]),
                    h('span', {
                        style: {
                            display: 'inline-block',
                            float: 'right',
                            marginRight: '32px',
                        },
                    }, [
                        h('i-button', {
                            props: {
                                size: 'small',
                                type: 'ghost',
                            },
                            style: {
                                marginRight: '8px',
                            },
                            on: {
                                click() {
                                    const self = this;
                                    window.console.log(self.createModal);
                                    window.console.log(data);
                                    window.console.log(root);
                                    window.console.log(node);
                                },
                            },
                        }, '编辑'),
                        h('i-button', {
                            props: {
                                size: 'small',
                                type: 'ghost',
                            },
                            on: {
                                click() {
                                    this.remove(root, node, data);
                                },
                            },
                        }, '删除'),
                    ]),
                ]);
            },
            editModalShow(data) {
                const self = this;
                self.createModal.title = '编辑';
                self.createModal.name = data.title;
                self.modalCreate = true;
            },
            append(data) {
                const children = data.children || [];
                children.push({
                    title: 'appended node',
                    expand: true,
                });
                this.$set(data, 'children', children);
            },
            remove(root, node, data) {
                const parentKey = root.find(el => el === node).parent;
                const parent = root.find(el => el.nodeKey === parentKey).node;
                const index = parent.children.indexOf(data);
                parent.children.splice(index, 1);
            },
        },
    };
</script>
<template>
    <div class="member-warp">
        <div class="user-manager-message user-manager-create">
            <div class="return-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span v-if="parent.type === '0'">信息管理-新增</span>
                <span v-if="parent.type === '1'">信息管理-编辑"{{ parent.name }}"</span>
            </div>
            <card :bordered="false">
                <i-form ref="form" :model="form" :rules="rules" :label-width="200">
                    <row>
                        <i-col span="12">
                            <form-item label="信息项名称" prop="name">
                                <i-input v-model="form.name"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="信息项介绍" prop="intro">
                                <i-input v-model="form.intro"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="信息项类型" prop="type">
                                <i-select v-model="form.type">
                                    <i-option v-for="item in types"
                                              :disabled="item.value === 0"
                                              :value="item.value"
                                              :key="item">{{ item.label }}</i-option>
                                </i-select>
                            </form-item>
                            <form-item label="字符限定" prop="num"
                                       v-if="form.type === 1 || form.type === 2">
                                <i-input number v-model="form.num"></i-input>
                                <p class="tip">最多可填写的字符数</p>
                            </form-item>
                            <form-item label="可选值" prop="check_value"
                                       v-if="form.type === 3 ||
                                           form.type === 4 ||
                                           form.type === 5 ||
                                           form.type === 8">
                                <i-input :autosize="{minRows: 3,maxRows: 5}"
                                         type="textarea"
                                         v-model="form.check_value"></i-input>
                                <p class="tip">每行输入一个可选值，使用回车键换行</p>
                            </form-item>
                            <form-item label="可选数量" prop="check_num"
                                       v-if="form.type === 4 || form.type === 5">
                                <i-input number v-model="form.check_num"></i-input>
                            </form-item>
                            <form-item label="时间组件类型" prop="check_time" v-if="form.type === 6">
                                <i-select v-model="form.check_time">
                                    <i-option v-for="item in timeTypes"
                                              :value="item.value">
                                        {{ item.label }}</i-option>
                                </i-select>
                            </form-item>
                            <form-item label="时间组件类型" prop="time_area" v-if="form.type === 7">
                                <i-select v-model="form.time_area">
                                    <i-option v-for="item in timeTypesArea"
                                              :value="item.value">
                                        {{ item.label }}</i-option>
                                </i-select>
                            </form-item>
                            <form-item label="大小限定" prop="limit_size"
                                       v-if="form.type === 10 || form.type === 11">
                                <i-input number v-model="form.limit_size"></i-input>
                                <p class="tip" v-if="form.type === 10">上传图片大小（单位：KB）</p>
                                <p class="tip" v-if="form.type === 11">上传文件大小（单位：KB）</p>
                            </form-item>
                            <form-item label="图片格式" prop="image_format"
                                       v-if="form.type === 10">
                                <checkbox-group v-model="form.image_format">
                                    <checkbox label="gif"></checkbox>
                                    <checkbox label="jpg"></checkbox>
                                    <checkbox label="bmp"></checkbox>
                                </checkbox-group>
                            </form-item>
                            <form-item label="文件格式" prop="file_format"
                                       v-if="form.type === 11">
                                <checkbox-group v-model="form.file_format">
                                    <checkbox label="doc"></checkbox>
                                    <checkbox label="txt"></checkbox>
                                    <checkbox label="rp"></checkbox>
                                </checkbox-group>
                            </form-item>
                        </i-col>
                    </row>
                    <row v-if="form.type === 9">
                        <i-col span="18">
                            <form-item label="可选值" prop="select_value">
                                <i-button type="ghost" class="btn-action"
                                          @click.native="addSelectValue">添加可选值</i-button>
                                <i-table :columns="columns"
                                         :data="list"
                                         v-if="selectValueList.length === 0"
                                         highlight-row>
                                </i-table>
                                <div class="tree-module" v-if="selectValueList.length > 0">
                                    <div class="tree-title">
                                        <span>可选值名称</span>
                                        <span>操作</span>
                                    </div>
                                    <tree :data="selectValueList"
                                          :render="renderContent"></tree>
                                </div>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="必填项" prop="status">
                                <i-switch size="large" v-model="form.status">
                                    <span slot="open">开启</span>
                                    <span slot="close">关闭</span>
                                </i-switch>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="排序" prop="sort">
                                <i-input number v-model="form.sort"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item>
                                <i-button :loading="loading" @click.native="submit"
                                          class="btn-group" type="primary">
                                    <span v-if="!loading">确认提交</span>
                                    <span v-else>正在提交…</span>
                                </i-button>
                            </form-item>
                        </i-col>
                    </row>
                </i-form>
            </card>
        </div>
        <modal
                v-model="modal"
                title="删除" class="setting-modal-delete">
            <div>
                <i-form ref="deleteModal" :model="deleteModal">
                    <row>
                        <i-col class="first-row-title delete-file-tip">
                            <span>确定要删除可选值"{{ deleteModal.name }}"吗？</span>
                        </i-col>
                    </row>
                    <row>
                        <i-col class="btn-group">
                            <i-button type="ghost" class="cancel-btn"
                                      @click.native="submitCancel">取消</i-button>
                            <i-button :loading="loading" type="primary" class="cancel-btn"
                                      @click.native="submitDelete">
                                <span v-if="!loading">确认</span>
                                <span v-else>正在删除…</span>
                            </i-button>
                        </i-col>
                    </row>
                </i-form>
            </div>
        </modal>
        <modal
                v-model="modalCreate"
                :title="createModal.title" class="setting-modal-delete setting-modal-action">
            <div>
                <i-form ref="createModal" :model="createModal" :rules="rulesModal" :label-width="110">
                    <row>
                        <i-col span="14">
                            <form-item label="可选值名称" prop="name">
                                <i-input v-model="createModal.name"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row v-if="createModal.title === '新增'">
                        <i-col span="14">
                            <form-item label="父级可选值" prop="last_name">
                                <cascader :data="parentList"
                                          change-on-select
                                          @on-change="filterDepartment"
                                          v-model="createModal.last_name"></cascader>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="14">
                            <form-item>
                                <i-button :loading="loading" @click.native="submitCreate"
                                          class="btn-group" type="primary">
                                    <span v-if="!loading">确认提交</span>
                                    <span v-else>正在提交…</span>
                                </i-button>
                            </form-item>
                        </i-col>
                    </row>
                </i-form>
            </div>
        </modal>
    </div>
</template>
