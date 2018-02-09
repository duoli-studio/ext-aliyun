<?php namespace System\Request\Develop;

use Illuminate\Http\Request;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\ViewTrait;
use System\Classes\Traits\SystemTrait;


class ToolController extends InitController
{
	use SystemTrait, ViewTrait;


	public function graphqlReverse(Request $request)
	{
		if (is_post()) {
			$content = $request->input('content');
			$lines   = explode("\n", $content);
			$changed = '';
			foreach ($lines as $line) {
				$newLine = trim($line, " \t\r\0\x0B\"+\\n");
				$changed .= $newLine . "\n";
			}

			return Resp::web(Resp::SUCCESS, '解析成功', [
				'content' => str_replace(['\"', '+'], '', $changed),
			]);
		}

		return view('system::develop.tool.graphql_reverse');
	}

	public function htmlEntity(Request $request)
	{
		if (is_post()) {
			$content = $request->input('content');
			return Resp::web(Resp::SUCCESS, '转化成功', [
				'content'        => htmlentities($content),
				'content_origin' => $content,
			]);
		}

		return view('system::develop.tool.html_entity');
	}

}
