<?php namespace Order\Models;


/**
 * @property integer $id                     主键id
 * @property string  $code                   服务器编码
 * @property string  $title                  服务器标题
 * @property string  $parent_id              父id
 * @property string  $top_parent_id          顶层ID, 父元素
 * @property string  $children               所有的子元素
 * @property string  $is_enable              是否启用 0:否,1:是
 * @property string  $is_default             是否默认 0:否,1:是
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
		'top_parent_id',
		'children',
		'is_enable',
		'is_default',
	];

}
