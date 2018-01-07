<?php namespace Slt\Request\Web;

use Illuminate\Http\Request;
use Poppy\Framework\Classes\Resp;
use System\Classes\Uploader;

/**
 * 服务接口
 * Class GameController
 * @package App\Http\Controllers\Desktop
 */
class UtilController extends InitController
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
		$Image = new Uploader('uploads');
		$Image->setExtension(['jpg', 'png', 'gif', 'jpeg']);
		$Image->setResizeDistrict(1000);
		if ($Image->saveFile($file)) {
			return Resp::web(Resp::SUCCESS, '图片上传成功', [
				'json'        => true,
				'success'     => true,
				'url'         => $Image->getUrl(),
				'destination' => $Image->getDestination(),
			]);
		}
		else {
			return Resp::web(Resp::ERROR, $Image->getError(), [
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
		$field = $request->input('field', 'bin_file');
		$ext   = $request->input('ext');

		// 匹配
		$file   = \Input::file($field);
		$Upload = new Uploader();
		$Upload->setExtension([$ext]);
		if ($Upload->saveFile($file)) {
			return Resp::web('OK~文件上传成功', [
				'json'        => true,
				'success'     => true,
				'url'         => $Upload->getUrl(),
				'destination' => $Upload->getDestination(),
			]);
		}
		else {
			return Resp::web(Resp::ERROR, $Upload->getError(), [
				'json' => true,
			]);
		}
	}
}