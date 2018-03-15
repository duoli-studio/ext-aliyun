<?php namespace Slt\Request\Web;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Poppy\Framework\Classes\Resp;
use Slt\Action\Book;
use Slt\Models\ArticleBook;
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
		return view('web.prd.book');
	}

	public function Item($id)
	{
		$book  = PrdBook::find($id);
		$items = PrdContent::where('top_parent_id', $id)->get();

		$array = [];
		// 构建生成树中所需的数据
		foreach ($items as $k => $r) {
			$item          = [];
			$item['title'] = $r->title;
			$item['id']    = $r->id;
			$item['sort']  = $r->list_order;
			$item['pid']   = $r->parent_id;
			$item['add']   = "<a class='J_iframe' href=\"" . route_url('web:prd.popup', null, ['parent_id' => $r->id, 'book_id' => $id]) . "\"><i class='iconfont icon-add'></i></a>";
			$item['edit']  = "<a href=\"" . route('web:prd.content', [$r->id]) . "\"><i class='fa fa-edit'></i></a>";
			$item['del']   = "<a class=\"J_request\" href='" . route('web:prd.destroy', [$r->id]) . "' data-confirm=\"确定删除该文档吗?\" ><i class='fa fa-close'></i></a>";
			$array[$r->id] = $item;
		}
		// gen html
		$str = <<<TABLE_LINE
<tr class="tr">
	<td class="txt-center"><input type="text" value="\$sort" class="w36 form-control input-sm" name="sort[\$id]"></td>
	<td class="txt-center">\$id</td>
	<td>\$spacer \$title </td>
	<td class="txt-center">
		  \$add \$edit  \$del
	</td>
</tr>
TABLE_LINE;

		$Tree = new Tree();
		$Tree->init($array);
		$html_tree = $Tree->getTree(0, $str);

		return view('web.prd.my_book_item', [
			'html_tree' => $html_tree,
			'book'      => $book,
		]);
	}


}

