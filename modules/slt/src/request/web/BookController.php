<?php namespace Slt\Request\Web;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Helper\TreeHelper;
use Slt\Action\Book;
use Slt\Models\ArticleBook;
use Slt\Models\ArticleContent;
use Sour\Lemon\Classes\Tree;
use Sour\Lemon\Helper\StrHelper;
use Sour\Poppy\Action\ActPrd;
use Sour\Poppy\Auth\FeUser;
use Sour\Poppy\Models\PrdBook;
use Sour\Poppy\Models\PrdContent;
use Sour\Poppy\Models\PrdTag;
use Sunra\PhpSimple\HtmlDomParser;

class BookController extends InitController
{

	use AuthorizesRequests;


	public function my()
	{
		$items = ArticleBook::where('account_id', $this->getWebPam()->id)
			->orderBy('created_at', 'desc')
			->paginate($this->pagesize);

		return view('slt::book.my', [
			'items' => $items,
		]);
	}

	/**
	 * 创建订单
	 * @param int $id
	 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function establish($id = null)
	{
		if (is_post()) {
			$Prd = (new Book())->setPam($this->getWebPam());
			if ($Prd->establish(\Input::all(), $id)) {
				return Resp::web(Resp::SUCCESS, '创建文库成功', 'top_reload|1');
			}
			else {
				return Resp::web(Resp::ERROR, $Prd->getError());
			}
		}
		return view('slt::book.establish');
	}

	public function show($id)
	{
		$book  = ArticleBook::find($id);
		$items = ArticleContent::where('book_id', $id)->get();

		$array = [];
		// 构建生成树中所需的数据
		foreach ($items as $k => $r) {
			$item          = [];
			$item['title'] = $r->title;
			$item['id']    = $r->id;
			$item['sort']  = $r->list_order;
			$item['pid']   = $r->parent_id;
			$item['add']   = "<a class='J_iframe' href=\"" . route_url('slt:article.popup', null, ['parent_id' => $r->id, 'book_id' => $id]) . "\"><i class='glyphicon glyphicon-plus'></i></a>";
			$item['edit']  = "<a href=\"" . route('slt:article.establish', [$r->id]) . "\"><i class='glyphicon glyphicon-pencil'></i></a>";
			$item['del']   = "<a class=\"J_request\" href='" . route('slt:article.destroy', [$r->id]) . "' data-confirm=\"确定删除该文档吗?\" ><i class='glyphicon glyphicon-remove'></i></a>";
			$array[$r->id] = $item;
		}
		// gen html
		$str = <<<TABLE_LINE
<tr class=\"tr\">
	<td class=\"txt-center\"><input type=\"text\" value=\"\$sort\" class=\"w36 form-control input-sm\" name=\"sort[\$id]\"></td>
	<td class=\"txt-center\">\$id</td>
	<td>\$spacer \$title </td>
	<td class=\"txt-center\">
		  \$add \$edit  \$del
	</td>
</tr>
TABLE_LINE;

		$Tree = new TreeHelper();
		$Tree->init($array);
		$html_tree = $Tree->getTree(0, $str);

		return view('slt::book.show', [
			'html_tree' => $html_tree,
			'book'      => $book,
		]);
	}


}

