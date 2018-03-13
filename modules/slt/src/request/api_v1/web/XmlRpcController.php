<?php namespace Slt\Request\ApiV1\Web;

use Illuminate\Http\Request;
use Poppy\Framework\Classes\Resp;
use Slt\Action\Article;
use Slt\Classes\MetaWeblog\Response as MetaWeblogResponse;
use Slt\Classes\Contracts\Creator as CreatorContract;
use Slt\Classes\Contracts\XmlRpc as XmlRpcContract;
use Slt\Classes\Traits\SltTrait;
use Slt\Models\ArticleBook;
use System\Action\Pam;
use Slt\Classes\MetaWeblog\Request as MetaWeblogRequest;

/**
 * 文档 https://codex.wordpress.org/XML-RPC_MetaWeblog_API
 * Class XmlRpcController
 */
class XmlRpcController implements CreatorContract, XmlRpcContract
{
	use SltTrait;

	private $client_ip;

	/** @var  Resp */
	private $resp;


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
		$this->client_ip = $request->getClientIp();
		/**
		 * 消息处理，具体函数细节，可参看
		 * @link http://php.net/manual/zh/book.xmlrpc.php
		 * 接受客户端POST过来的XML数据
		 */
		$request  = $request->getContent();
		$method   = null;
		$response = xmlrpc_decode_request($request, $method);
		/**
		 * 如果用户名密码不正确
		 */
		list($appkey, $username, $password) = $response;
		if (!$this->authenticate($username, $password)) {
			$this->creatorFail($this->resp->getMessage());
			exit();
		}
		/**
		 * 正常执行
		 */
		if (isset($methods[$method])) {
			call_user_func_array([$this, $methods[$method]], [$method, $response]);
		}
		else {
			$this->creatorFail("The method you requested, '$method', was not found.");
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
		$response = [
			'faultCode'   => '2',
			'faultString' => $error,
		];
		MetaWeblogResponse::response($response, 'error');
	}

	/**
	 * Observer creator Success
	 * @param $model
	 */
	public function creatorSuccess($model)
	{
		MetaWeblogResponse::response($model->id);
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
			$this->resp = $Pam->getError();
			return false;
		}
	}


	/**
	 * 获取用户的所有的 blog
	 * @param $params
	 */
	public function getUsersBlogs($method, $params)
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
	 * @param $method
	 * @param $params
	 */
	public function editPost($method, $params)
	{
		list($post_id, $username, $password, $struct, $publish) = $params;
		$request = $this->transform($struct);
		app(\Persimmon\Creator\PostsCreator::class)->update($this, $request);
	}

	/**
	 * Get Categories
	 * @param $method
	 * @param $params
	 */
	public function getCategories($method, $params)
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
	 * @param $method
	 * @param $params
	 */
	public function getPost($method, $params)
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
	 * @param $method
	 * @param $params
	 */
	public function getRecentPosts($method, $params)
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
	 * @param $method
	 * @param $params
	 */
	public function newMediaObject($method, $params)
	{
		list($blogid, $username, $password, $struct) = $params;
		//开始上传
		$qiniu = new QiniuUploads();
		$url   = $qiniu->putForContent($struct['bits']);
		MetaWeblogResponse::response(['url' => $url]);
	}

	/**
	 * Create new Post
	 * @param $method
	 * @param $params
	 */
	public function newPost($method, $params)
	{
		list($blogid, $username, $password, $struct, $publish) = $params;
		$request = $this->transform($struct);
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
	 * @param $method
	 * @param $params
	 */
	public function newCategory($method, $params)
	{
		list($blog_id, $username, $password, $category) = $params;
		$categorys = Categorys::firstOrCreate(['category_name' => $category]);
		MetaWeblogResponse::response(intval($categorys->id));
	}

	/**
	 * delete Post
	 * @param $method
	 * @param $params
	 */
	public function deletePost($method, $params)
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
			'ipaddress'   => !empty($this->client_ip) ? $this->client_ip : '127.0.0.1',
		];
		return $request;
	}


}

