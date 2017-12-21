<?php namespace Order\Request\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Lemon\Dailian\Action\ActionAliOss;
use App\Lemon\Dailian\Application\App\AppWeb;
use Illuminate\Http\Request;

/**
 * 服务接口
 * Class GameController
 * @package App\Http\Controllers\Desktop
 */
class UploadController extends Controller {
	/**
	 * 上传回调
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getReturn(Request $request) {
		$upload_return = $request->input('upload_return');
		echo json_encode(json_decode(base64_decode($upload_return)));
	}

	/**
	 * 图片上传组件的后端
	 * @param Request $request
	 *    'default'    => 'image_file',
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function postImage(Request $request) {
		$field = $request->input('field', 'image_file');
		// 匹配
		$file  = \Input::file($field);
		$Image = new ActionAliOss();
		$Image->setExtension(['jpg','png','gif','jpeg']);
		if ($Image->saveLocal($file) && $Image->saveAli()) {
			return AppWeb::resp(AppWeb::SUCCESS, '图片上传成功', [
				'json'        => true,
				'success'     => true,
				'url'         => $Image->getUrl(),
				'destination' => $Image->getDestination(),
			]);
		} else {
			return AppWeb::resp(AppWeb::ERROR, $Image->getError(), [
				'json' => true,
			]);
		}
	}
}
