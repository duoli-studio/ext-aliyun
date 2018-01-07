<?php namespace Slt\Request\Web\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Sour\Lemon\Classes\Tree;
use Sour\Lemon\Helper\StrHelper;
use Sour\Lemon\Support\Resp;
use Sour\Poppy\Action\ActPrd;
use Sour\Poppy\Auth\FeUser;
use Sour\Poppy\Models\PrdBook;
use Sour\Poppy\Models\PrdContent;
use Sour\Poppy\Models\PrdTag;
use Sunra\PhpSimple\HtmlDomParser;

class PrdController extends InitController
{

	use AuthorizesRequests;

	/**
	 * 创建订单
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
	 */
	public function create()
	{
		if (is_post()) {
			$Prd = (new ActPrd())->setUser(FeUser::instance());
			if (!$Prd->handle(\Input::all())) {
				return Resp::web($Prd->getError());
			}
			$prd = $Prd->getPrd();
			return Resp::web('OK~博文发表成功', 'location|' . route('front_prd.show', [$prd->id]));
		}
		$title       = \Input::get('title');
		$parent_id   = \Input::get('parent_id');
		$topParentId = PrdContent::topParentId($parent_id);
		if (!$topParentId) {
			return Resp::web('来源不正确, 请重新输入');
		}
		$titles = PrdContent::parentTitles($parent_id, true);

		return view('web.prd.create', [
			'title'         => $title,
			'parent_id'     => $parent_id,
			'top_parent_id' => $topParentId,
			'titles'        => $titles,
		]);
	}

	public function myBook()
	{
		$items = PrdBook::where('account_id', FeUser::instance()->getPam()->id)
			->orderBy('created_at', 'desc')
			->paginate($this->pagesize);

		return view('web.prd.my_book', [
			'items' => $items,
		]);
	}

	public function myBookItem($id)
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
<tr class='tr'>
	<td class='txt-center'><input type='text' value='\$sort' class='w36 form-control input-sm' name='sort[\$id]'></td>
	<td class='txt-center'>\$id</td>
	<td>\$spacer \$title </td>
	<td class='txt-center'>
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


	/**
	 * 创建订单
	 * @param int $id
	 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function popup($id = 0)
	{
		if (is_post()) {
			$Prd = (new ActPrd())->setUser(FeUser::instance());
			if ($Prd->establishPopup(\Input::all(), $id)) {
				$prd = $Prd->getPrd();
				return Resp::web('OK~创建文档成功', 'top_location|' . route('web:prd.my_book_item', [$prd->top_parent_id]));
			}
			else {
				return Resp::web($Prd->getError());
			}
		}
		$book_id = \Input::get('book_id');
		if ($id) {
			$item    = PrdContent::find($id);
			$book_id = $item->top_parent_id;
			\View::share('item', $item);
		}
		if (!$book_id) {
			return Resp::web('不正确的数据');
		}
		$articles = PrdContent::where('top_parent_id', $book_id)->get();
		$items    = [];
		if ($articles->count()) {
			foreach ($articles as $prd) {
				$items[$prd->id] = [
					'id'    => $prd->id,
					'title' => $prd->title,
					'pid'   => $prd->parent_id,
				];
			}
		}
		return view('web.prd.popup_item', [
			'items'   => $items,
			'book_id' => $book_id,
		]);
	}

	/**
	 * 创建订单
	 * @param int $id
	 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function book($id = 0)
	{
		if (is_post()) {
			$Prd = (new ActPrd())->setUser(FeUser::instance());
			if ($Prd->establishBook(\Input::all(), $id)) {
				return Resp::web('OK~创建文库成功', 'top_reload|1');
			}
			else {
				return Resp::web($Prd->getError());
			}
		}
		return view('web.prd.book');
	}


	public function content($id)
	{
		if (is_post()) {
			$Prd = (new ActPrd())->setUser(FeUser::instance());
			if ($Prd->handle(\Input::all(), $id)) {
				return Resp::web('OK~编辑成功!');
			}

		}
		/** @type PrdContent $item */
		$item = PrdContent::find($id);
		$this->authorize('self', $item);
		$item->prd_tag = PrdTag::unformat($item->prd_tag, ',');
		$parent_id     = $item->parent_id;
		$top_parent_id = PrdContent::topParentId($item->id);
		$titles        = PrdContent::parentTitles($item->id, true);
		return view('web.prd.content', [
			'item'          => $item,
			'parent_id'     => $parent_id,
			'top_parent_id' => $top_parent_id,
			'title'         => $item->title,
			'titles'        => $titles,
		]);
	}


	/**
	 * @param $id
	 * @return mixed
	 */
	public function show($id)
	{
		/** @type PrdContent $prd */
		$prd = PrdContent::find($id);
		if ($prd->account_id != $this->pam->id) {
			return Resp::web('您无权访问此页面!');
		}
		$html         = StrHelper::markdownToHtml($prd->content_origin);
		$prd->content = PrdContent::mdInlineLink($html, $prd->id, true);
		$parent_id    = $prd->parent_id;
		$parent       = null;
		if ($prd->parent_id) {
			$parent    = PrdContent::find($prd->parent_id);
			$parent_id = $parent->id;
		}

		$content = $prd->content;
		// find a link
		if (preg_match_all("|<a[^>]+>.*</[^>]+>|U", $content, $out, PREG_SET_ORDER)) {
			// replace
			foreach ($out as $a) {
				$origin = $a[0];
				if ($origin) {
					$dom = HtmlDomParser::str_get_html($origin);
					if ($dom->find('a', 0)) {
						$href = $dom->find('a', 0)->href;
						if (strpos($href, config('app.url')) === false) {
							$dom->find('a', 0)->target = '_blank';
						}
						$content = str_replace($origin, $dom, $content);
					}
				}
			}
		}
		$prd->content = $content;

		$levelTitles = PrdContent::parentTitles($id, false, false);
		return view('web.prd.show', [
			'item'         => $prd,
			'parent_id'    => $parent_id,
			'parent'       => $parent,
			'parent_url'   => route('front_prd.show', [$parent_id]),
			'level_titles' => $levelTitles,
		]);
	}

	public function name($parent_id, $name)
	{
		/** @var PrdContent $parent */
		$parent = PrdContent::findOrFail($parent_id);
		if ($parent->parent_id) {
			$parent = PrdContent::findOrFail($parent_id);
		}
		$topParentId = PrdContent::topParentId($parent_id);
		$topParent   = PrdContent::find($topParentId);
		$item        = PrdContent::where('top_parent_id', $topParentId)
			->where('title', trim($name))
			->first();
		if ($item) {
			return redirect(route('front_prd.show', [$item->id]));
		}

		$levelTitles = PrdContent::parentTitles($parent_id, false, true);

		return view('web.prd.show', [
			'item'          => $item,
			'title'         => $name,
			'parent_id'     => $parent_id,
			'top_parent_id' => $topParentId,
			'top_parent'    => $topParent,
			'parent'        => $parent,
			'level_titles'  => $levelTitles,
		]);
	}

	public function postStatusTrash($id)
	{
		if (!is_array($id)) {
			$id = [$id];
		}
		PrdContent::whereIn('id', $id)->update([
			'status' => PrdContent::STATUS_TRASH,
		]);
		return Resp::web('OK~移动到垃圾桶', 'reload|1');
	}

	public function status($id, $status)
	{
		if (!PrdContent::kvStatus($status, true)) {
			return Resp::web('状态错误');
		}
		if (!is_array($id)) {
			$id = [$id];
		}

		PrdContent::whereIn('id', $id)->update([
			'status' => $status,
		]);
		return Resp::web('OK~操作成功', 'reload|1');
	}


	public function postStatusPost($id)
	{
		if (!is_array($id)) {
			$id = [$id];
		}
		PrdContent::whereIn('id', $id)->update([
			'status' => PrdContent::STATUS_POST,
		]);
		return Resp::web('OK~发布成功', 'reload|1');
	}

	public function postStatusDraft($id)
	{
		if (!is_array($id)) {
			$id = [$id];
		}
		PrdContent::whereIn('id', $id)->update([
			'status' => PrdContent::STATUS_DRAFT,
		]);
		return Resp::web('OK~成功移动到草稿箱', 'reload|1');
	}


	public function address($prd_id)
	{
		if (!$prd_id) {
			return Resp::web('原型id不能为空');
		}
		/** @var PrdContent $prd */
		$prd = PrdContent::find($prd_id);
		if (empty($prd)) return Resp::web('原型不存在');

		$url      = route_url('front_prd.view', null, ['id' => $prd->id]);
		$code_url = route_url('support_util.qrcode_image', null, [
			't' => $url,
		]);

		return view('web.prd.address', [
			'item'     => $prd,
			'url'      => $url,
			'code_url' => $code_url,
		]);
	}


	/**
	 * 获取原型地址访问
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function view(Request $request)
	{
		$id = $request->input('id');
		if (!$id) return Resp::web('原型id不能为空');
		/** @var PrdContent $prd */
		$prd = PrdContent::find($id);
		if (empty($prd)) {
			return Resp::web('原型不存在');
		}

		$topParentId = PrdContent::topParentId($id);

		// 检测原型是否加密
		// 已经加密并且没有密码
		if (
		($prd->role_status == PrdContent::ROLE_STATUS_PWD && !\Session::has('prd_view_' . $topParentId))
		) {
			return view('web.prd.view_pwd', [
				'item'     => $prd,
				'id_crypt' => $prd->id,
			]);
		}
		else {
			$html         = StrHelper::markdownToHtml($prd->content_origin);
			$prd->content = PrdContent::mdInlineLink($html, $prd->id, false);
			$parent_id    = $prd->parent_id;
			$parent       = null;
			if ($prd->parent_id) {
				$parent    = PrdContent::find($prd->parent_id);
				$parent_id = $parent->id;
			}

			$levelTitles = PrdContent::parentTitles($id, false, true);
			return view('web.prd.view_content', [
				'item'         => $prd,
				'level_titles' => $levelTitles,
				'parent_id'    => $parent_id,
				'parent'       => $parent,
				'parent_url'   => route_url('front_prd.view', null, ['id' => $parent_id]),
			]);
		}
	}

	/**
	 * 获取原型地址访问
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function detail($id)
	{
		if (!$id) return Resp::web('原型id不能为空');
		/** @var PrdContent $prd */
		$prd = PrdContent::with('front')->find($id);
		if (empty($prd)) {
			return Resp::web('原型不存在');
		}

		if ($prd->type == PrdContent::TYPE_PRIVATE) {
			return Resp::web('私有文档不允许查看');
		}

		$content      = str_replace([
			'<body>', '<html>', '</body>', '</html>',
		], '', $prd->content);
		$html         = StrHelper::markdownToHtml($content);
		$prd->content = PrdContent::mdInlineLink($html, $prd->id, false);
		$parent_id    = $prd->parent_id;
		$parent       = null;
		if ($prd->parent_id) {
			$parent    = PrdContent::find($prd->parent_id);
			$parent_id = $parent->id;
		}

		$levelTitles = PrdContent::parentTitles($id, false, true);


		$has_good     = false;
		$has_bad      = false;
		$has_transfer = false;


		return view('web.prd.detail', [
			'item'         => $prd,
			'level_titles' => $levelTitles,
			'parent_id'    => $parent_id,
			'parent'       => $parent,
			'has_good'     => $has_good,
			'has_transfer' => $has_transfer,
			'has_bad'      => $has_bad,
			'_title'       => $prd->title,
			'parent_url'   => route('front_prd.detail', $parent_id),
		]);
	}

	public function viewName($parent_id, $name)
	{
		/** @var PrdContent $parent */
		$parent = PrdContent::findOrFail($parent_id);
		if ($parent->parent_id) {
			$parent = PrdContent::findOrFail($parent_id);
		}
		$topParentId = PrdContent::topParentId($parent_id);
		/** @var PrdContent $topParent */
		$topParent = PrdContent::find($topParentId);
		$item      = PrdContent::where('top_parent_id', $topParentId)
			->where('prd_title', trim($name))
			->first();
		if ($item) {
			return redirect(route_url('front_prd.view', null, ['id' => $item->id]));
		}

		$levelTitles = PrdContent::parentTitles($parent_id, true);

		return view('web.prd.view_name', [
			'item'          => $item,
			'title'         => $name,
			'parent_id'     => $parent_id,
			'top_parent_id' => $topParentId,
			'top_parent'    => $topParent,
			'parent'        => $parent,
			'level_titles'  => $levelTitles,
			'parent_url'    => route_url('front_prd.view', null, ['id' => $this->encode($parent_id)]),
		]);
	}


	/**
	 * 点赞
	 * @param $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function good($id)
	{
		// 添加赞
		PrdStar::updateOrCreate([
			'prd_id'     => $id,
			'account_id' => $this->pam->account_id,
			'type'       => PrdStar::TYPE_GOOD,
		], [
			'prd_id'     => $id,
			'account_id' => $this->pam->account_id,
			'type'       => PrdStar::TYPE_GOOD,

			'ip' => \Input::getClientIp(),
		]);

		// 删除踩
		PrdStar::where([
			'prd_id'     => $id,
			'account_id' => $this->pam->account_id,
			'type'       => PrdStar::TYPE_BAD,
		])->delete();

		/** @var PrdContent $prd */
		$prd           = PrdContent::find($id);
		$prd->good_num = PrdStar::typeNum($id);
		$prd->bad_num  = PrdStar::typeNum($id, PrdStar::TYPE_BAD);
		$prd->save();

		\Event::fire('daniu.active_update', [$this->front]);
		\Event::fire('daniu.prd_good', [$this->front, $prd]);

		return Resp::web('OK~+1', [
			'good_num' => $prd->good_num,
			'bad_num'  => $prd->bad_num,
		]);
	}


	public function bad($id)
	{
		// 检查是否已经踩过
		PrdStar::updateOrCreate([
			'prd_id'     => $id,
			'account_id' => $this->pam->id,
			'type'       => PrdStar::TYPE_BAD,
		], [
			'prd_id'     => $id,
			'account_id' => $this->pam->id,
			'type'       => PrdStar::TYPE_BAD,
			'ip'         => \Input::getClientIp(),
		]);

		// 删除赞
		PrdStar::where([
			'prd_id'     => $id,
			'account_id' => $this->pam->id,
			'type'       => PrdStar::TYPE_GOOD,
		])->delete();

		/** @var PrdContent $prd */
		$prd           = PrdContent::find($id);
		$prd->good_num = PrdStar::typeNum($id);
		$prd->bad_num  = PrdStar::typeNum($id, PrdStar::TYPE_BAD);
		$prd->save();

		return Resp::web('OK~-1', [
			'good_num' => $prd->good_num,
			'bad_num'  => $prd->bad_num,
		]);
	}

	//权限设置
	public function access(Request $request, $prd_id)
	{
		if (is_post()) {
			if (!$prd_id) return Resp::web('原型id不能为空');
			$validator = \Validator::make($request->all(), [
				'access' => 'required|integer',
			], [
				'access.required' => '权限类型必须选择',
			]);
			if ($validator->fails()) {
				return Resp::web($validator->errors());
			}

			$access   = $request->input('access');
			$password = $request->input('password');
			if ($access) {
				if (empty($password)) return Resp::web('密码不能为空');
			}

			$data = [
				'role_status' => $access,
				'password'    => $password,
			];
			PrdContent::where('id', $prd_id)->update($data);
			return Resp::web('OK~权限设置成功', 'reload_opener|1;time|1000');
		}
		if (!$prd_id) {
			return Resp::web('原型id不能为空');
		}
		/** @var PrdContent $prd */
		$prd = PrdContent::find($prd_id);
		if (empty($prd)) return Resp::web('原型不存在');

		return view('web.prd.access', [
			'item' => $prd,
		]);
	}

}

