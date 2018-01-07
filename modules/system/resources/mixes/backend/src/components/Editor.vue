<script>
	export default {
		name    : 'Editor',
		data() {
			return {
				// 为了避免麻烦，每个编辑器实例都用不同的 id
				randomId : `editor_${(Math.random() * 100000000000000000)}`,
				instance : null,
			};
		},
		created() {
			if (window.UE !== undefined) {
				// 如果全局对象存在，说明编辑器代码已经初始化完成，直接加载编辑器
				this.initEditor();
			}
			else {
				// 如果全局对象不存在，说明编辑器代码还没有加载完成，需要加载编辑器代码
				this.insertScriptTag();
			}
		},
		beforeDestroy() {
			// 组件销毁的时候，要销毁 UEditor 实例
			if (this.instance !== null && this.instance.destroy) {
				this.instance.destroy();
			}
		},
		methods : {
			insertScriptTag() {
				const loading = [];
				const self = this;
				let configScriptTag = document.getElementById('configScriptTag');
				let editorScriptTag = document.getElementById('editorScriptTag');
				// 如果这个tag不存在，则生成相关代码tag以加载代码
				if (configScriptTag === null) {
					loading.push(new Promise((resolve, reject) => {
						configScriptTag = document.createElement('script');
						configScriptTag.type = 'text/javascript';
						configScriptTag.src = `${self.path}neditor.config.js`;
						configScriptTag.id = 'configScriptTag';
						if (configScriptTag.readyState) {
							configScriptTag.onreadystatechange = () => {
								if (configScriptTag.readyState === 'loaded' || configScriptTag.readyState === 'complete') {
									configScriptTag.onreadystatechange = null;
									resolve(configScriptTag);
								}
							};
						}
						else {
							configScriptTag.onload = () => {
								resolve(configScriptTag);
							};
						}
						configScriptTag.onerror = () => {
							reject(Error('Configuration load error!'));
						};
						document.body.appendChild(configScriptTag);
					}));
				}
				if (editorScriptTag === null) {
					loading.push(new Promise((resolve, reject) => {
						editorScriptTag = document.createElement('script');
						editorScriptTag.type = 'text/javascript';
						editorScriptTag.src = `${self.path}neditor.all.min.js`;
						editorScriptTag.id = 'editorScriptTag';
						if (editorScriptTag.readyState) {
							editorScriptTag.onreadystatechange = () => {
								if (editorScriptTag.readyState === 'loaded' || editorScriptTag.readyState === 'complete') {
									editorScriptTag.onreadystatechange = null;
									resolve(editorScriptTag);
								}
							};
						}
						else {
							editorScriptTag.onload = () => {
								resolve(editorScriptTag);
							};
						}
						editorScriptTag.onerror = () => {
							reject(Error('Editor load error!'));
						};
						document.body.appendChild(editorScriptTag);
					}));
				}
				// 等待代码加载完成后初始化编辑器
				Promise.all(loading).then(() => {
					setTimeout(() => {
						self.initEditor();
					}, 300);
				});
			},
			initEditor() {
				const self = this;
				if (self.instance === null) {
					// Vue 异步执行 DOM 更新，这样一来代码执行到这里的时候可能 template 里面的 script 标签还没真正创建
					// 所以，我们只能在 nextTick 里面初始化 UEditor
					self.$nextTick(() => {
						self.instance = window.UE.getEditor(self.randomId, self.config);
						// 绑定事件，当 UEditor 初始化完成后，将编辑器实例通过自定义的 ready 事件交出去
						self.instance.addListener('contentChange', () => {
							self.$emit('input', self.instance.getContent());
						});
						self.instance.addListener('ready', () => {
							self.instance.setContent(self.value);
							self.$emit('ready', self.instance);
						});
					});
				}
			},
		},
		props   : {
			config : {
				// UEditor 配置项
				type : Object,
				default() {
					return {
						initialFrameHeight : 600,
						topOffset          : 89,
					};
				},
			},
			path   : {
				// UEditor 代码的路径
				type    : String,
				default : '/',
			},
			value  : {
				type    : String,
				default : '',
			},
		},
	};
</script>
<template>
    <div :id="randomId" name="content" type="text/plain"></div>
</template>