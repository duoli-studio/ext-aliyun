<?php namespace Slt\Classes\Contracts;


use Illuminate\Http\Request;

interface XmlRpc
{
	public function on(Request $request);

	public function errorMessage();

	public function authenticate($email, $password);

	public function getUsersBlogs($method, $params);

	public function editPost($method, $params);

	public function getCategories($method, $params);

	public function getPost($method, $params);

	public function getRecentPosts($method, $params);

	public function newMediaObject($method, $params);

	public function newPost($method, $params);

	public function newCategory($method, $params);

	public function deletePost($method, $params);
}