<?php namespace Slt\Request\Web\Controllers;

use Sour\Lemon\Helper\FileHelper;
use Sour\Lemon\Helper\StrHelper;
use Sunra\PhpSimple\HtmlDomParser;
use Symfony\Component\Process\Process;


class TestController extends InitController
{
	private $selfMenu;

	/**
	 * FeController constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->selfMenu = [
			'Js文档'         => [
				'archive' => false,
				'url'     => route('web:fe.js'),
			],
			'酸柠檬'          => [
				'archive' => false,
				'url'     => '',
			],
			'修复 global 错误' => [
				'archive' => true,
				'url'     => '',
			],
			'js文档'         => [
				'archive' => true,
				'url'     => '',
			],
			'metronic'     => [
				'archive' => true,
				'url'     => 'http://fe.sour-lemon.com/fex/metronic/',
			],
			'inspinia'     => [
				'archive' => true,
				'url'     => 'http://fe.sour-lemon.com/fex/inspinia/',
			],
		];
	}


	/**
	 * 首页
	 * @param null $plugin
	 * @return mixed
	 */
	public function index($plugin = null)
	{
		phpinfo();
	}

}
