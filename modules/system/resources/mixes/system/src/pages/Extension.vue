<script>
	import injection, {trans} from '../helpers/injection';

	export default {
		beforeRouteEnter(to, from, next) {
			injection.loading.start();
			injection.http.post(`${window.api}/g/backend`, {
				query : 'query {extensions{authors,description,enabled,identification,initialized,installed}}',
			}).then(response => {
				const {extensions} = response.data.data;
				next(vm => {
					vm.extensions = Object.keys(extensions).map(index => {
						const extension = extensions[index];
						extension.loading = {
							install   : false,
							uninstall : false,
						};

						return extension;
					});
					window.console.log(vm.extensions);
					injection.loading.finish();
				});
			}).catch(() => {
				injection.loading.error();
			});
		},
		data() {
			const self = this;

			return {
				columns    : [
					{
						key   : 'description',
						title : '拓展名称',
						width : 200,
					},
					{
						key   : 'authors',
						title : '作者',
						width : 300,
					},
					{
						key   : 'installed',
						render(h, data) {
							if (data.row.installed === true) {
								if (data.row.requireUninstall === true) {
									return '待卸载';
								}

								return '已安装';
							}
							if (data.row.requireInstall === true) {
								return '待安装';
							}

							return '未安装';
						},
						title : '安装状态',
					},
					{
						key   : 'handler',
						render(h, data) {
							return h('div', [
								h('i-button', {
									on    : {
										click() {
											self.extensions[data.index].loading.install = true;
											self.$http.post(`${window.api}/administration/extensions`, {
												identification : data.row.identification,
											}).then(() => {
												self.$notice.open({
													desc  : '请前往控制器执行命令 php notadd extension:install，以执行拓展的安装！',
													title : '添加到待安装列表成功！',
												});
												self.refresh();
											}).catch(() => {
												self.$notice.error({
													title : '添加到待安装列表失败！',
												});
											}).finally(() => {
												self.extensions[data.index].loading.install = false;
											});
										},
									},
									props : {
										disabled : data.row.installed || data.row.requireInstall,
										loading  : self.extensions[data.index].loading.install,
										size     : 'small',
										type     : 'primary',
									},
									style : {
										marginRight : '10px',
									},
								}, '添加到待安装列表'),
								h('i-button', {
									on    : {
										click() {
											const identification = data.row.identification.replace('/', '-');
											self.extensions[data.index].loading.uninstall = true;
											self.$http.delete(`${window.api}/administration/extensions/${identification}`).then(() => {
												self.$notice.open({
													desc  : '请前往控制器执行命令 php notadd extension uninstall，以执行拓展的卸载！',
													title : '添加到待卸载列表成功！',
												});
												self.refresh();
											}).catch(() => {
												self.$notice.error({
													title : '添加到待卸载列表失败！',
												});
											}).finally(() => {
												self.extensions[data.index].loading.uninstall = false;
											});
										},
									},
									props : {
										disabled : !data.row.installed || data.row.requireUninstall,
										loading  : self.extensions[data.index].loading.uninstall,
										size     : 'small',
										type     : 'error',
									},
								}, '添加到待卸载列表'),
							]);
						},
						title : '操作',
						width : 400,
					},
				],
				extensions : [],
			};
		},
		methods : {
			refresh() {
				const self = this;
				self.$notice.open({
					title : '正在刷新数据...',
				});
				self.$http.get(`${window.api}/administration/extensions`).then(response => {
					const extensions = response.data.data;
					self.extensions = Object.keys(extensions).map(index => {
						const extension = extensions[index];
						extension.loading = {
							install   : false,
							uninstall : false,
						};

						return extension;
					});
					self.$notice.open({
						title : '属性数据成功！',
					});
					self.$loading.finish();
				}).catch(() => {
					self.$notice.error({
						title : '刷新数据失败！',
					});
					self.$loading.error();
				});
			},
		},
		mounted() {
			this.$store.commit('title', trans('administration.title.expand'));
		},
	};
</script>
<template>
    <div class="module-wrap">
        <tabs value="installed">
            <tab-pane label="拓展配置" name="installed">
                <card :bordered="false">
                    <i-table :columns="columns" :data="extensions"></i-table>
                </card>
            </tab-pane>
        </tabs>
    </div>
</template>