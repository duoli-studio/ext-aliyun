<?php namespace System\Models;


/**
 * System\Models\SysArea
 *
 * @property int    $id
 * @property string $code     编码
 * @property string $province 省份
 * @property string $city     城市
 * @property string $district 区
 * @property string $parent   父级
 * @mixin \Eloquent
 */
class SysArea extends \Eloquent
{

	protected $table = 'sys_area';

	public $timestamps = false;

	protected $fillable = [
		'code',
		'province',
		'city',
		'district',
		'parent',
	];

}

