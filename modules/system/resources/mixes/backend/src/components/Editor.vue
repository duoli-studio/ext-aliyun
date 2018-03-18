<script>
import 'quill/dist/quill.core.css';
import 'quill/dist/quill.snow.css';
import 'quill/dist/quill.bubble.css';
import {quillEditor, Quill} from 'vue-quill-editor';
import {container, ImageExtend, QuillWatch} from 'quill-image-extend-module';

Quill.register('modules/ImageExtend', ImageExtend);

export default {
	name       : 'Editor',
	components : {
		quillEditor
	},
	data() {
		return {
			myValue      : this.value,
			editorOption : {
				debug   : 'info',
				modules : {
					ImageExtend : {
						loading  : true,
						name     : 'image',
						action   : `${window.upload}`,
						response : (res) => {
							const {data} = res;
							return data.url[0];
						},
						headers  : (xhr) => {
							xhr.setRequestHeader('Authorization', `Bearer ${window.localStorage.getItem('token')}`);
						},
					},
					toolbar     : {
						container,
						handlers : {
							image() {
								QuillWatch.emit(this.quill.id);
							}
						}
					}
				}
			}
		};
	},
	methods    : {
		onEditorChange(item) {
			this.$emit('on-value-change', item.html);
		}
	},
	model      : {
		prop  : 'value',
		event : 'on-value-change'
	},
	watch      : {
		result(val) {
			this.myValue = val; // 新增result的watch，监听变更并同步到myResult上
		}
	},
	props      : {
		value : {
			type    : String,
			default : '',
		},
	},
};
</script>
<template>
	<div class="quill-wrap">
		<quill-editor
				v-model="myValue"
				:options="editorOption"
				@change="onEditorChange($event)">
		</quill-editor>
	</div>
</template>