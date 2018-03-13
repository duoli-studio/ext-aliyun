<?php namespace Slt\Action;


use Illuminate\Validation\Rule;
use Slt\Classes\Traits\SltTrait;
use Slt\Models\ArticleBook;
use Slt\Models\ArticleContent;
use Slt\Models\SiteTag;

class Article
{

	use SltTrait;

	/** @type  ArticleContent */
	private $article;

	/** @var ArticleBook */
	private $book;

	private $prdTable;

	public function __construct()
	{
		$this->prdTable = (new ArticleContent())->getTable();
	}


	public function establish($data, $id = null)
	{

		if (!$this->checkPam()) {
			return false;
		}

		// handle blog
		$data = $this->data($data);

		$validator = \Validator::make($data, [
			'content' => 'required',
		], [
			'content.required' => '文档内容不能为空',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->errors());
		}

		if ($id) {
			if (!$this->init($id)) {
				return false;
			}
			if (!$this->pam->can('edit', [$this->article])) {
				return $this->setError('此文档不是您创建, 您无权操作');
			}
			$oldContent = $this->article->content_origin;
			$this->article->update($data);
		}
		else {
			$oldContent    = '';
			$this->article = ArticleContent::create($data);
			// $this->prd->top_parent_id = ArticleContent::topParentId($this->prd->id);
			// $this->prd->save();
		}


		// 处理标签关联
		// $Tag = new ActPrdTag();
		// $Tag->handle($this->article);

		// 版本创建
		if (md5($oldContent) != md5($this->article->content_origin)) {
			// $Version = new ActPrdVersion();
			// $Version->create($this->article);
		}
		return true;
	}

	/**
	 * 标题创建
	 * @param array $data
	 * @param null  $id
	 * @return bool
	 */
	public function establishPopup(array $data, $id = null)
	{
		// login check
		if (!$this->checkPam()) {
			return false;
		}

		// rule check
		$cat_id    = intval(array_get($data, 'cat_id'));
		$rule      = [
			'title'   => [
				'required',
				Rule::unique($this->prdTable, 'title')->where(function($query) use ($id, $cat_id) {
					$query->where('account_id', $this->pam->id);
					if ($id) {
						$query->where('id', '!=', $id);
					}
					if ($cat_id) {
						$query->where('cat_id', $id);
					}
				}),
			],
			'book_id' => 'required',
		];
		$validator = \Validator::make($data, $rule, [], [
			'title'   => '文档标题',
			'book_id' => '文库',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->errors());
		}

		// db handle
		// init
		$data = [
			'title'         => strval(array_get($data, 'title')),
			'account_id'    => $this->pam->id,
			'cat_id'        => $cat_id,
			'parent_id'     => intval(array_get($data, 'parent_id')),
			'top_parent_id' => array_get($data, 'book_id'),
		];

		if ($id) {
			// edit
			if (!$this->init($id)) {
				return false;
			}
			if (!$this->policy('self', [$this->article], '此文档不是您创建, 您无权操作')) {
				return false;
			}
			$this->article->update($data);
		}
		else {
			$data = array_merge($data, [
				'good_num'       => 0,
				'bad_num'        => 0,
				'role_status'    => ArticleContent::ROLE_STATUS_NONE,
				'password'       => '',
				'description'    => '',
				'content_origin' => '',
				'author'         => '',
				'icon'           => '',
				'list_order'     => 0,
				'hits'           => 0,
				'is_star'        => 0,
				'tag_note'       => '',
				'prd_tag'        => '',
			]);

			// db create
			$this->article = ArticleContent::create($data);
		}
		return true;
	}


	/**
	 * 标题创建
	 * @param array $data
	 * @param null  $id
	 * @return bool
	 */
	public function establishBook(array $data, $id = null)
	{
		// login check
		if (!$this->checkPam()) {
			return false;
		}

		// rule check
		$rule      = [
			'title' => [
				'required',
				Rule::unique($this->prdTable, 'title')->where(function($query) use ($id) {
					$query->where('account_id', $this->pam->id);
					if ($id) {
						$query->where('id', '!=', $id);
					}
				}),
			],
		];
		$validator = \Validator::make($data, $rule, [], [
			'title' => '文库',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->errors());
		}

		// db handle
		// init
		$data = [
			'title'      => strval(array_get($data, 'title')),
			'account_id' => $this->pam->id,
		];

		if ($id) {
			// edit
			if (!$this->initBook($id)) {
				return false;
			}
			if (!$this->policy('self', [$this->book], '此文库不是您创建, 您无权操作')) {
				return false;
			}
			$this->book->update($data);
		}
		else {
			$data = array_merge($data, [
				'account_id' => $this->pam->id,
				'is_favor'   => 0,
			]);

			// db create
			$this->book = ArticleBook::create($data);
		}
		return true;
	}

	/**
	 * 初始化条目信息
	 * @param $id
	 * @return bool
	 */
	public function init($id)
	{
		if (!ArticleContent::where('id', $id)->exists()) {
			return $this->setError('此条目不存在!');
		}
		$this->article = ArticleContent::find($id);
		return true;
	}

	/**
	 * 初始化条目信息
	 * @param $id
	 * @return bool
	 */
	public function initBook($id)
	{
		if (!ArticleBook::where('id', $id)->exists()) {
			return $this->setError('此条目不存在!');
		}
		$this->book = ArticleBook::find($id);
		return true;
	}


	public function data($data)
	{
		if (isset($data['prd_tag']) && $data['prd_tag']) {
			$data['prd_tag'] = SiteTag::encode($data['prd_tag']);
		}
		if (isset($data['_token'])) {
			unset($data['_token']);
		}
		if (!isset($data['prd_content'])) {
			$data['prd_content'] = '';
		}
		if ($this->pam) {
			$data['account_id'] = $this->pam->id;
		}
		return $data;
	}

	public function getArticle()
	{
		return $this->article;
	}

	public function getBook()
	{
		return $this->book;
	}


}