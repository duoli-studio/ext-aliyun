<?php namespace Slt\Request\Web;


class HomeController extends InitController
{


	public function index()
	{
		return view('slt::home.index');
	}

	public function vue()
	{
		return view('slt::home.vue');
	}

}
