<?php namespace Order\Models;

use Illuminate\Database\Eloquent\Builder;
use Poppy\Framework\Classes\Traits\KeyParserTrait;

/**
 * @property integer $id                 主键id
 * @property string  $code        服务器编码
 * @property string  $title       服务器标题
 * @property string  $parent_id          父id
 * @mixin \Eloquent
 * @method static Builder|GameServer applyKey($key)
 */
class GameServer extends \Eloquent
{

	use KeyParserTrait;

	protected $table = 'game_server';

	protected $primaryKey = 'id';

	public $timestamps = false;

	protected $fillable = [
		'id',
		'code',
		'title',
		'parent_id',
	];

	/**
	 * 生成服务器编码
	 */
	/*public static function genCode()
	{
		// 更新并且保存 code
		$item->server_code = sprintf("%'.04d%'.04d%'.04d", $item->game_id, $item->id, 0);
		$item->save();
	}*/

	public static function genCode()
	{
		/** @var GameServer[] $all */
		$all = self::get();
		foreach ($all as $item)
		{
			$id = self::where('id',$item->parent_id)->value('id');
			$parent_id = self::where('id',$id)->value('parent_id');

			if ($item->parent_id === 0){
				$item->code = sprintf("%'.04d%'.04d%'.04d", $item->id, 0, 0);
				$item->save();
			}
			elseif($parent_id === 0){
				$item->code = sprintf("%'.04d%'.04d%'.04d", $id, $parent_id, 0);
				$item->save();
			}
		}


	}
}
