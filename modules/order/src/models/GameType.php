<?php namespace App\Models;

use Carbon\Carbon;


/**
 * App\Models\GameType
 * @property integer       $type_id    代练type
 * @property integer       $game_id    游戏id
 * @property string        $type_title 标题
 * @property integer       $list_order 排序
 * @property Carbon        $created_at
 * @property string        $deleted_at
 * @property Carbon        $updated_at
 * @property-read GameName $game
 */
class GameType extends \Eloquent {

	protected $table = 'game_type';

	protected $primaryKey = 'type_id';

	public $timestamps = true;

	protected $fillable = [
		'game_id',
		'type_title',
	];

	public function game() {
		return $this->belongsTo('App\Models\GameName', 'game_id');
	}

	/**
	 * 游戏类型的kv 显示
	 * @param      $game_id
	 * @param null $key
	 * @return array|mixed|string
	 */
	public static function kvTypeTitle($game_id, $key = null) {
		$names = GameType::where('game_id', $game_id)->orderBy('list_order', 'asc')->get();
		$data  = [];
		foreach ($names as $name) {
			$data[$name->type_id] = $name->type_title;
		}
		return kv($data, $key);
	}

}
