<?php namespace Slt\Request\Web;

use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Resp;

/**
 * 工具
 * Class ToolController
 * @package App\Http\Controllers\Web
 */
class ToolController extends Controller
{


	/**
	 * @param string $type
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector|\Illuminate\View\View
	 */
	public function index($type = 'xml')
	{
		if (!in_array($type, ['xml', 'json', 'css', 'sql'])) {
			return Resp::web(Resp::ERROR, '格式化类型不正确');
		}
		return view('slt::tool.index', [
			'type' => $type,
		]);
	}

}