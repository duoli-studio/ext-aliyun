<?php namespace Slt\Request\ApiV1\Web;

use Comodojo\Exception\XmlrpcException;
use Comodojo\Xmlrpc\XmlrpcDecoder;
use Illuminate\Http\Request;
use Poppy\Framework\Classes\Resp;
use Slt\Action\Article;
use Slt\Classes\Contracts\Creator as CreatorContract;
use Slt\Classes\Contracts\XmlRpc as XmlRpcContract;
use Slt\Classes\MetaWeblog\Response as MetaWeblogResponse;
use Slt\Classes\Traits\SltTrait;
use Slt\Models\ArticleBook;
use System\Action\OssUploader;
use System\Action\Pam;

/**
 * 文档 https://codex.wordpress.org/XML-RPC_MetaWeblog_API
 * Class XmlRpcController
 */
class XmlRpcController implements CreatorContract, XmlRpcContract
{
	use SltTrait;

	private $clientIp;

	private $postId;

	private $username;

	private $password;


	/**
	 * @param Request $request
	 */
	public function on(Request $request)
	{
		$methods = [
			'blogger.getUsersBlogs'     => 'getUsersBlogs',
			'blogger.deletePost'        => 'deletePost',
			'metaWeblog.newPost'        => 'newPost',
			'metaWeblog.editPost'       => 'editPost',
			'metaWeblog.getPost'        => 'getPost',
			'metaWeblog.getCategories'  => 'getCategories',
			'metaWeblog.newMediaObject' => 'newMediaObject',
			'metaWeblog.getRecentPosts' => 'getRecentPosts',
			'wp.newCategory'            => 'newCategory',
		];
		/**
		 * 获取客户端IP
		 */
		$this->clientIp = $request->getClientIp();
		/**
		 * 消息处理，具体函数细节，可参看
		 * @link http://php.net/manual/zh/book.xmlrpc.php
		 * 接受客户端POST过来的XML数据
		 */
		$request = $request->getContent();
		$method  = null;

		// create a decoder instance
		$decoder = new XmlrpcDecoder();
		try {
			$response       = $decoder->decodeCall($request);
			$method         = $response[0] ?? '';
			$params         = $response[1] ?? [];
			$this->postId   = $params[0] ?? 0;
			$this->username = $params[1] ?? '';
			$this->password = $params[2] ?? '';

			\Log::debug($method, $params);

			// 验证用户名
			if (!$this->authenticate($this->username, $this->password)) {
				$this->creatorFail($this->getError());
				exit();
			}
			/**
			 * 正常执行
			 */
			if (isset($methods[$method])) {
				call_user_func_array([$this, $methods[$method]], [$params]);
			}
			else {
				$this->creatorFail("The method you requested, '$method', was not found.");
			}
		} catch (XmlrpcException $e) {
			$this->creatorFail($e->getMessage());
		}
	}

	/**
	 * Get Request Show Error Message
	 */
	public function errorMessage()
	{
		return response('XML-RPC server accepts POST requests only.');
	}

	/**
	 * Observer creator Fail
	 * @param $error
	 */
	public function creatorFail($error)
	{
		MetaWeblogResponse::response([
			'faultCode'   => '2',
			'faultString' => $error,
		], 'error');
		exit();
	}

	/**
	 * Observer creator Success
	 * @param $model
	 */
	public function creatorSuccess($model)
	{
		MetaWeblogResponse::response($model->id);
		exit();
	}

	/**
	 * authenticate
	 * @param $email
	 * @param $password
	 * @return bool
	 */
	public function authenticate($email, $password)
	{
		$Pam = new Pam();
		if ($Pam->loginCheck($email, $password)) {
			$this->pam = $Pam->getPam();
			return true;
		}
		else {
			return $this->setError($Pam->getError());
		}
	}


	/**
	 * 获取用户的所有的 blog
	 * @param $params
	 */
	public function getUsersBlogs($params)
	{
		$response[0] = [
			'url'      => url('/'),
			'blogid'   => !empty($appkey) ? $appkey : '1',
			'blogName' => 'WuliDoc',
		];

		// $items = PrdBook::where('account_id', $this->pam->id)->get();

		MetaWeblogResponse::response($response);
	}

	/**
	 * edit Post
	 * @param $params
	 */
	public function editPost($params)
	{
		$request = $this->transform($params[3]);
		$Article = (new Article())->setPam($this->pam);
		if ($Article->establish($request, $this->postId)) {
			MetaWeblogResponse::response($Article->getArticle()->id);
		}
		else {
			$this->creatorFail($Article->getError());
		}
	}

	/**
	 * Get Categories
	 * @param $params
	 */
	public function getCategories($params)
	{
		$books    = ArticleBook::where('account_id', $this->pam->id)->get();
		$category = [];
		foreach ($books as $book) {
			$category[] = $book;
		}
		MetaWeblogResponse::response($category);
	}

	/**
	 * Get Post
	 * @param $params
	 */
	public function getPost($params)
	{
		list($post_id, $username, $password) = $params;
		$data                = [];
		$post                = Posts::where('id', $post_id)->select('id', 'id as postid', 'title', 'category_id', 'markdown as description', 'user_id as userid', 'flag as wp_slug', 'created_at as dateCreated')->first();
		$data                = $post->toArray();
		$tags                = $post->tags->toArray();
		$data['categories']  = $post->categories->category_name;
		$data['link']        = route('posts', [$post->wp_slug]);
		$tags                = array_map(function($item) {
			return $item['tags_name'];
		}, $tags);
		$data['mt_keywords'] = implode(',', $tags);
		unset($post, $tags);
		MetaWeblogResponse::response($data);
	}

	/**
	 * Get Recent Posts
	 * @param $params
	 */
	public function getRecentPosts($params)
	{
		list($blogid, $username, $password, $numberOfPosts) = $params;
		$posts = Posts::orderBy('id', 'desc')->select('id', 'id as postid', 'title', 'category_id', 'markdown as description', 'user_id as userid', 'flag as wp_slug', 'created_at as dateCreated')->paginate($numberOfPosts);
		$data  = [];
		foreach ($posts as $key => $post) {
			$data[$key]                = $post->toArray();
			$tags                      = $post->tags->toArray();
			$data[$key]['categories']  = $post->categories->category_name;
			$data[$key]['link']        = route('posts', [$post->wp_slug]);
			$tags                      = array_map(function($item) {
				return $item['tags_name'];
			}, $tags);
			$data[$key]['mt_keywords'] = implode(',', $tags);
			unset($post, $tags);
		}
		MetaWeblogResponse::response($data);
	}

	/**
	 * Upload new Media Object
	 * @param array $params
	 * @throws \Illuminate\Container\EntryNotFoundException
	 */
	public function newMediaObject($params)
	{
		$data = $params[3] ?? [];

		$Image = new OssUploader('article');
		$Image->setResizeDistrict(1920);
		$Image->setExtension(['jpg', 'png', 'gif', 'jpeg', 'bmp']);
		if ($Image->saveInput($data['bits'])) {
			MetaWeblogResponse::response(['url' => $Image->getUrl()]);
		}
		else {
			$this->creatorFail('上传图片失败:' . $Image->getError());
		}
	}

	/**
	 * Create new Post
	 * @param $params
	 */
	public function newPost($params)
	{
		$request = $this->transform($params[3]);
		$Article = (new Article())->setPam($this->pam);
		if ($Article->establish($request)) {
			MetaWeblogResponse::response($Article->getArticle()->id);
		}
		else {
			$this->creatorFail($Article->getError());
		}
	}

	/**
	 * Create new Category
	 * @param $params
	 */
	public function newCategory($params)
	{
		list($blog_id, $username, $password, $category) = $params;
		$categorys = Categorys::firstOrCreate(['category_name' => $category]);
		MetaWeblogResponse::response(intval($categorys->id));
	}

	/**
	 * delete Post
	 * @param $params
	 */
	public function deletePost($params)
	{
		list($appKey, $postid, $username, $password, $publish) = $params;
		$result = Posts::where('id', $postid)->delete();
		MetaWeblogResponse::response(intval($result) > 0 ? true : false);
	}

	/**
	 * method Not Found
	 * @param $methodName
	 */
	protected function methodNotFound($methodName)
	{
		$this->creatorFail("The method you requested, '$methodName', was not found.");
	}

	/**
	 * transform data
	 * @param $structure
	 * @return array
	 */
	private function transform($structure)
	{
		$tags    = strpos($structure['mt_keywords'], ',') !== false ? explode(',', $structure['mt_keywords']) : $structure['mt_keywords'];
		$request = [
			'title'       => $structure['title'],
			'flag'        => $structure['wp_slug'],
			'thumb'       => '',
			'tags'        => is_array($structure['mt_keywords']) ? $structure['mt_keywords'] : $tags,
			'category_id' => 0,
			'user_id'     => $this->pam->id,
			'content'     => $structure['description'],
			'ipaddress'   => !empty($this->clientIp) ? $this->clientIp : '127.0.0.1',
		];
		return $request;
	}


}

