<?php namespace System\Request\ApiV1\Util;

use Poppy\Framework\Application\ApiController;
use Poppy\Framework\Classes\Resp;
use System\Action\OssUploader;
use System\Classes\Traits\SystemTrait;

class ImageController extends ApiController
{
	use SystemTrait;

	/**
	 * @api                 {post} api_v1/util/image/upload [O]图片上传
	 * @apiDescription      图片上传
	 * @apiVersion          1.0.0
	 * @apiName             UtilImageUpload
	 * @apiGroup            Util
	 * @apiParam   {String} image         图片内容(支持多张/单张上传)
	 * @apiParam   {String} [type]        上传图片的类型(form,base64) [form|表单(默认)]
	 * @apiParam   {String} [image_type]  图片图片存储类型(default)默认default
	 */
	public function upload()
	{
		$type       = \Input::get('type', 'form');
		$image_type = \Input::get('image_type', 'default');

		$all               = \Input::all();
		$all['image_type'] = $image_type ?: 'default';
		$all['type']       = $type;

		$validator = \Validator::make($all, [
			'image'      => 'required',
			'type'       => 'required|in:form,base64',
			'image_type' => 'required|in:default',
		], [], [
			'type'       => '上传图片的类型',
			'image'      => '图片内容',
			'image_type' => '图片存储类型',
		]);
		if ($validator->fails()) {
			return Resp::web(Resp::ERROR, $validator->messages());
		}

		$Image = new OssUploader($image_type);
		if ($image_type == 'default') {
			$Image->setResizeDistrict(1920);
		}

		$urls = [];
		if ($type == 'form') {
			$Image->setExtension(['jpg', 'png', 'gif', 'jpeg', 'bmp']);
			$image = (\Input::file('image'));
			if (!is_array($image)) {
				$image = [$image];
			}

			foreach ($image as $_img) {
				if ($Image->saveFile($_img)) {
					$urls[] = $Image->getUrl();
				}
			}
		}
		elseif ($type == 'base64') {
			$image = \Input::get('image');

			if (!is_array($image)) {
				$image = [$image];
			}
			foreach ($image as $_img) {
				$content = base64_decode($_img);
				if ($Image->saveInput($content)) {
					$urls[] = $Image->getUrl();
				}
			}
		}

		// 上传图
		if (count($urls)) {
			return Resp::web(Resp::SUCCESS, '上传成功', [
				'url'  => $urls,
				'json' => 1,
			]);
		}
		 
			return Resp::web(Resp::ERROR, $Image->getError());
	}
}