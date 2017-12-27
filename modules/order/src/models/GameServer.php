<?php namespace Order\Models;


/**
 * @property integer $id                 主键id
 * @property string  $code               服务器编码
 * @property string  $title              服务器标题
 * @property string  $parent_id          父id
 * @mixin \Eloquent
 */
class GameServer extends \Eloquent
{

	protected $table = 'game_server';

	public $timestamps = false;

	protected $fillable = [
		'code',
		'title',
		'parent_id',
	];

}
