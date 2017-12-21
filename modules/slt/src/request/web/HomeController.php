<?php namespace Slt\Request\Web\Controllers;


class HomeController extends InitController
{


	public function index()
	{
		echo trans('slt::tip.safe_money');
		return view('slt::home.index');
	}

	public function vue()
	{
		return view('slt::home.vue');
	}

}
