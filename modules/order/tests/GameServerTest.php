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
			'title'     => 'sdfsdfs',
			'parent_id' => '15',
		]);
		dd($game->getError());
	}


	public function testGenId()
	{
		$game = app('act.server');
		$code = $game->parentId(14,0);
		dd($code);
	}

	public function testGenCode()
	{
		$game = app('act.server');
		$code = $game->genCode(9);
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
