<?php namespace Futian\Request\Backend;

use System\Request\Backend\InitController;


class HomeController extends InitController
{

    /**
     * 控制面板
     * @return \Illuminate\View\View
     */
    public function cp()
    {
        echo 'cp @ futian';
        return view('system::backend.home.cp');
    }

}