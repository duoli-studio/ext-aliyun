<?php namespace Slt\Request\Web;


use Poppy\Framework\Application\Controller;
use Slt\Classes\Traits\SltTrait;


class InitController extends Controller
{
	use SltTrait;

	public function __construct()
	{
		parent::__construct();
		// 自动计算seo
		// 根据路由名称来转换 seo key
		// slt:nav.index  => slt::seo.nav_index
		$seoKey = str_replace([':', '.'], ['::', '_'], $this->route);
		if ($seoKey) {
			$seoKey = str_replace('::', '::seo.', $seoKey);
			$this->seo(trans($seoKey));
		}
	}
}