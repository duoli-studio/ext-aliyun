<?php namespace System\Tests\Pam;

/**
 * Copyright (C) Update For IDE
 */
use Poppy\Framework\Application\TestCase;
use System\Models\Resources\HelpResource;
use System\Models\SysHelp;

class HelpTest extends TestCase
{
	public function testShow()
	{
		dd(new HelpResource(SysHelp::all()->toArray()));
		// dd(Help::collection(SysHelp::where('type','关于猎手')->get()->toArray()));
		dd((new HelpResource(SysHelp::with('category')->where('type', '关于猎手')->first())));

		// dd(SysHelp::with('category')->first());
		dd(SysHelp::with('category')->where('type', '关于猎手')->first());
	}
}
