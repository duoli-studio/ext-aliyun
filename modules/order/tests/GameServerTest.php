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
		// $game = app('act.actGameServer');
		$game = new \Order\Action\ActGameServer();
		$item = $game->establish([
			'title'     => '微信大区2',
			'parent_id' => '6',
		]);
		dd($game->getError());
	}

	public function testGenId()
	{
		$game = new \Order\Action\ActGameServer();
		$code = $game->genCode(4);
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
