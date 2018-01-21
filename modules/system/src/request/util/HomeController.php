<?php namespace System\Request\Util;


use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Helper\UtilHelper;
use System\Classes\Traits\SystemTrait;
use System\Models\SysArea;
use System\Util\Action\ImageCaptcha;
use System\Util\Action\OssUploader;


class HomeController extends Controller
{
	use SystemTrait;


	/**
	 * @api                 {get} /util/captcha [O]图像验证码
	 * @apiDescription      通过url地址过去验证图片, 用于发送验证码的流量限制
	 * @apiVersion          1.0.0
	 * @apiName             UtilCaptcha
	 * @apiGroup            Util
	 * @apiParam   {String} mobile   手机号
	 */
	public function captcha()
	{
		$input     = [
			'mobile' => \Input::get('mobile'),
		];
		$validator = \Validator::make($input, [
			'mobile' => 'required|mobile',
		], [], [
			'mobile' => '手机号',
		]);
		if ($validator->fails()) {
			return Resp::web(Resp::ERROR, $validator->messages(), 'json|1');
		}
		return (new ImageCaptcha())->generate($input['mobile']);
	}

	/**
	 * @api                 {post} /util/image [O]图片上传
	 * @apiDescription      图片上传
	 * @apiVersion          1.0.0
	 * @apiName             UtilImage
	 * @apiGroup            Util
	 * @apiParam   {String} image      图片内容
	 * @apiParam   {String} type       上传图片的类型(form,base64)默认form
	 * @apiParam   {String} image_type 图片图片存储类型(default)默认default
	 */
	public function image()
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

		if ($type == 'form') {
			$content = \Input::file('image');
			$Image->setExtension(['jpg', 'png', 'gif', 'jpeg']);
			$saveResult = $Image->saveFile($content);
		}
		else {
			$image      = \Input::get('image');
			$content    = base64_decode($image);
			$saveResult = $Image->saveInput($content);
		}

		// 上传图
		if ($saveResult) {
			return Resp::web(Resp::SUCCESS, '上传成功', [
				'url'  => $Image->getUrl(),
				'json' => 1,
			]);
		}
		else {
			return Resp::web(Resp::ERROR, $Image->getError());
		}
	}

	/**
	 * @api                  {get} /util/area 地区获取
	 * @apiVersion           1.0.0
	 * @apiName              UtilArea
	 * @apiGroup             Util
	 * @apiSuccess {String}  id                                 ID
	 * @apiSuccess {String}  title                              地区名称
	 * @apiSuccess {String}  areas                              地区
	 * @apiSuccessExample    data:
	 * {
	 *      "id":2
	 *      "title":"山东省"
	 *      "areas":{
	 *          "id":15
	 *          "title":"济南市"
	 *          "areas":{
	 *              "id":17
	 *              "title":"历城区"
	 *                  }
	 *              }
	 *      "message":"获取地区成功！"
	 *      "status":0
	 * }
	 */
	public function area()
	{
		$items = SysArea::get()->toArray();
		$array = UtilHelper::genTree($items, 'id', 'parent_id', 'areas');

		$return = [];
		foreach ($array as $province_key => $province_value) {
			$new_province_value = [
				'id'    => $province_value['id'],
				'title' => $province_value['title'],
			];
			if (!isset($province_value['areas'])) {
				continue;
			}
			foreach ($province_value['areas'] as $city_key => $city_value) {
				$new_city_value = [
					'id'    => $city_value['id'],
					'title' => $city_value['title'],
				];
				if (!isset($city_value['areas'])) {
					continue;
				}
				foreach ($city_value['areas'] as $area_key => $area_value) {
					$new_area_value            = [
						'id'    => $area_value['id'],
						'title' => $area_value['title'],
					];
					$new_city_value['areas'][] = $new_area_value;
				}

				$new_province_value['areas'][] = $new_city_value;
			}
			$return[$province_key] = $new_province_value;
		}

		return $this->getResponse()->json([
			'data'    => $return,
			'message' => '获取地区成功！',
			'status'  => Resp::SUCCESS,
		], 200, [], JSON_UNESCAPED_UNICODE);

	}
}
