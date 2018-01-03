<script>
	import injection from '../helpers/injection';

	export default {
		beforeRouteEnter(to, from, next) {
			injection.loading.start();
			injection.http.post(`${window.api}/administration/seo/module`).then(response => {
				const list = response.data.data;
				next(vm => {
					vm.list = Object.keys(list).map(index => list[index]);
					injection.loading.finish();
				});
			}).catch(() => {
				injection.loading.error();
			});
		},
		data() {
			const self = this;

			return {
				columns : [
					{
						key   : 'order',
						render(h, data) {
							const store = data.row;
							if (store.identification === 'global') {
								return '/';
							}

							return h('tooltip', {
								props       : {
									placement : 'right-end',
								},
								scopedSlots : {
									content() {
										return '回车以修改数据';
									},
									default() {
										return h('i-input', {
											on    : {
												'on-change' : event => {
													store.order = event.target.value;
												},
												'on-enter'  : () => {
													self.list[data.index].order = store.order;
													self.update(data.index);
												},
											},
											props : {
												value : data.row.order,
											},
										});
									},
								},
							});
						},
						title : '匹配排序',
						width : 100,
					},
					{
						key   : 'name',
						title : '名称',
						width : 300,
					},
					{
						key   : 'alias',
						title : '别名/域名',
					},
					{
						key   : 'handle',
						render(h, data) {
							return h('router-link', {
								props : {
									to : `/seo/${data.row.identification}`,
								},
							}, [
								h('i-button', {
									props : {
										size : 'small',
										type : 'primary',
									},
								}, '编辑'),
							]);
						},
						title : '操作',
						width : 300,
					},
				],
				list    : [],
			};
		},
		methods : {
			update(index) {
				const self = this;
				const data = self.list[index];
				self.$loading.start();
				self.$http.post(`${window.api}/administration/seo/order`, data).then(() => {
					self.$loading.finish();
					self.$notice.open({
						title : '更新模块匹配排序数据成功！',
					});
					self.refresh();
				}).catch(() => {
					self.$loading.error();
					self.$notice.error({
						title : '更新模块匹配排序数据失败！',
					});
				});
			},
			refresh() {
				const self = this;
				self.$notice.open({
					title : '正在刷新数据...',
				});
				self.$loading.start();
				self.$http.post(`${window.api}/administration/seo/module`).then(response => {
					self.list = Object.keys(response.data.data).map(index => response.data.data[index]);
					self.$loading.finish();
					self.$notice.open({
						title : '刷新数据成功！',
					});
				}).catch(() => {
					self.$loading.error();
					self.$notice.error({
						title : '刷新数据失败！',
					});
				});
			},
		},
	};
</script>
<template>
    <div class="seo-wrap">
        <tabs>
            <tab-pane label="SEO 模板">
                <card :bordered="false">
                    <i-table :columns="columns" :data="list"></i-table>
                </card>
            </tab-pane>
        </tabs>
    </div>
</template>