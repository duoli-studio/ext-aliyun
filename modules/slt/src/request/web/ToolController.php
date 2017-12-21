<?php namespace Slt\Request\Web\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Sour\Lemon\Support\Resp;
use Sour\System\Action\SystemUpload;

/**
 * 工具
 * Class ToolController
 * @package App\Http\Controllers\Web
 */
class ToolController extends Controller
{


	public function index($type = 'xml')
	{
		if (!in_array($type, ['xml', 'json', 'css', 'sql'])) {
			return Resp::web('格式化类型不正确');
		}
		return view('web.tool.index', [
			'type' => $type,
		]);
	}

}