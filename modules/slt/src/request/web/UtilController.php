<?php namespace Slt\Request\Web\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Sour\Lemon\Support\Resp;
use Sour\System\Action\SystemUpload;

/**
 * 服务接口
 * Class GameController
 * @package App\Http\Controllers\Desktop
 */
class UtilController extends Controller
{

	/**
	 * 上传回调
	 * @param Request $request
	 */
	public function uploadReturn(Request $request)
	{
		$upload_return = $request->input('upload_return');
		echo json_encode(json_decode(base64_decode($upload_return)));
	}


	public function image(Request $request)
	{
		$field = $request->input('field', 'image_file');
		// 匹配
		$file  = \Input::file($field);
		$Image = new SystemUpload();
		$Image->setExtension(['jpg', 'png', 'gif', 'jpeg']);
		if ($Image->saveFile($file) && $Image->saveAli()) {
			return Resp::web('OK~图片上传成功', [
				'json'        => true,
				'success'     => true,
				'url'         => $Image->getUrl(),
				'destination' => $Image->getDestination(),
			]);
		}
		else {
			return Resp::web($Image->getError(), [
				'json' => true,
			]);
		}
	}

	/**
	 * 上传文件
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function file(Request $request)
	{
		$field    = $request->input('field', 'bin_file');
		$ext      = $request->input('ext');
		$save_ali = $request->input('save_ali');
		// 匹配
		$file   = \Input::file($field);
		$Upload = new SystemUpload();
		$Upload->setExtension([$ext]);
		if ($Upload->saveFile($file)) {
			if ($save_ali) {
				// save error
				if (!$Upload->saveAli(true)) {
					return Resp::web($Upload->getError(), [
						'json' => true,
					]);
				}
			}
			return Resp::web('OK~文件上传成功', [
				'json'        => true,
				'success'     => true,
				'url'         => $Upload->getUrl(),
				'destination' => $Upload->getDestination(),
			]);
		}
		else {
			return Resp::web($Upload->getError(), [
				'json' => true,
			]);
		}
	}
}