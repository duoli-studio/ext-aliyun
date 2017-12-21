<?php namespace App\Models;


/**
 * App\Models\GameName
 * @property integer        $game_id    id
 * @property string         $game_name  游戏名称
 * @property integer        $list_order 排序
 * @property \Carbon\Carbon $created_at
 * @property string         $deleted_at
 * @property \Carbon\Carbon $updated_at
 */
class GameName extends \Eloquent {


	protected $table = 'game_name';

	protected $primaryKey = 'game_id';

	protected $fillable = [
		'game_name',
		'yq_game_name'
	];

	/**
	 * 获取游戏的 kv 列表
	 */
	public static function getAll() {
		$names = GameName::orderBy('list_order', 'asc')->get()->toArray();
		$data  = [];
		foreach ($names as $name) {
			$data[$name['game_id']] = $name;
		}
		return $data;
	}

	/**
	 * 获取游戏的 kv 列表
	 */
	public static function kvLinear() {
		$names = self::getAll();
		$data  = [];
		foreach ($names as $name) {
			$data[$name['game_id']] = $name['game_name'];
		}
		return $data;
	}

	/**
	 * 通过Id 获取名称
	 * @param $name_id
	 * @return mixed
	 */
	public static function getName($name_id) {
		static $names;
		if (!$names) {
			$names = self::kvLinear();
		}
		return $names[$name_id];
	}

}
