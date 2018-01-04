<?php namespace Order\Tests;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;

class GameServerTest extends TestCase
{
	//
	public function testCreate()
	{
		$game = app('act.server');
		$item = $game->establish([
			'title'     => '测试',
			'parent_id' => '0',
		]);
		dd($game->getError());
	}


	public function testGenId()
	{
		$game = app('act.server');
		$ids = [];
		$code = $game->parentId(18,$ids);
		dd($code);
	}


	/*public function testUpdate()
	{
		// $game = app('act.actGameServer');
		$game = new \Order\Action\ActGameServer();
		$item = $game->establish([
			'title' => '比尔吉沃特',
			'parent_id' => '3',
		],4);

		dd($game->getError());
	}*/
}
