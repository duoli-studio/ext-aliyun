<script>
export default {
	name    : 'Uploader',
	data() {
		return {
			action     : `${window.upload}`,
			headers    : {
				Authorization : `Bearer ${window.localStorage.getItem('token')}`
			},
			imgName    : '',
			imgUrl     : '',
			multiple   : this.max > 1,
			field      : 'image',
			visible    : false,
			uploadList : [],
		};
	},
	methods : {
		/**
		 * 图片上传成功处理
		 */
		uploadSuccess(response, file) {
			file.url = response.data.url[0].toString();
			this.$emit('on-image-uploaded', this.uploadList);
			this.onSuccess(this.uploadList);
		},
		/**
		 * 图片上传数量检测
		 * @returns {boolean}
		 */
		beforeUpload() {
			const check = this.images.length < this.max;
			if (!check) {
				this.$notice.warning({
					title : `最多上传${this.max}张图片`,
				});
			}
			return check;
		},
		/**
		 * 文件体积过大提示
		 * @param file
		 */
		exceededMaxSize(file) {
			this.$notice.warning({
				title : '图片体积过大',
				desc  : `文件 ${file.name} 太大, 不要超过 ${this.size} Kb`,
			});
		},
		/**
		 * 图片预览
		 * @param item
		 */
		onViewClick(item) {
			this.imgName = item.name;
			this.imgUrl = item.url;
			this.visible = true;
		},
		/**
		 * 删除图片
		 * @param file
		 */
		onRemoveClick(file) {
			const fileList = this.images;
			this.uploadList.splice(fileList.indexOf(file), 1);
			this.$emit('on-image-uploaded', this.uploadList);
			this.onRemove(this.uploadList);
		},
	},
	model   : {
		prop  : 'images',
		event : 'on-image-uploaded'
	},
	/**
	 * 更新的时候更新
	 */
	updated() {
		this.uploadList = this.$refs.upload.fileList;
	},
	props   : {
		images    : {
			type    : Array,
			default : [],
		},
		max       : {
			type    : Number,
			default : 1,
		},
		size      : {
			type    : Number,
			default : 2048,
		},
		onSuccess : {
			type : Function
		},
		onRemove  : {
			type : Function
		}
	},
};
</script>
<template>
	<div>
		<div class="liex-upload-list" v-for="item in uploadList">
			<div v-if="item.status === 'finished'">
				<img :src="item.url">
				<div class="liex-upload-list-cover">
					<icon type="ios-eye-outline" @click.native="onViewClick(item)"></icon>
					<icon type="ios-trash-outline" @click.native="onRemoveClick(item)"></icon>
				</div>
			</div>
			<div v-else>
				<progress v-if="item.showProgress" :percent="item.percentage"></progress>
			</div>
		</div>
		<upload
				ref="upload"
				style="display: inline-block;width:58px;"
				:name="field"
				:action="action"
				:max-size="size"
				:headers="headers"
				:format="['jpg','jpeg','png']"
				:show-upload-list="false"
				:default-file-list="images"
				:on-success="uploadSuccess"
				:on-exceeded-size="exceededMaxSize"
				:multiple="multiple"
				:before-upload="beforeUpload">
			<div v-if="images.length < max" class="liex-upload-select" style="width: 58px;height:58px;line-height: 58px;">
				<icon type="camera" size="20"></icon>
			</div>
		</upload>
		<modal title="查看图片" v-model="visible">
			<img :src="imgUrl" v-if="visible" style="width: 100%">
		</modal>
	</div>
</template>