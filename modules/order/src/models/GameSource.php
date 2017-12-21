<?php namespace App\Models;


/**
 * App\Models\GameSource
 * @property integer        $source_id   来源
 * @property string         $source_name 名称
 * @property integer        $list_order  排序
 * @property \Carbon\Carbon $created_at
 * @property string         $deleted_at
 * @property \Carbon\Carbon $updated_at
 */
class GameSource extends \Eloquent {

	protected $table = 'game_source';

	protected $primaryKey = 'source_id';

	protected $fillable = [
		'source_name',
	];


	/**
	 * 获取游戏的来源
	 * @return array
	 */
	public static function getAll() {
		$sources = GameSource::orderBy('list_order', 'asc')->get()->toArray();
		$data    = [];
		foreach ($sources as $item) {
			$data[$item['source_id']] = $item;
		}
		return $data;
	}


	/**
	 * 获取游戏的来源 kv 列表
	 * @param null $key
	 * @return array
	 */
	public static function kvSourceTitle($key = null) {
		$sources = self::getAll();
		$data    = [];
		foreach ($sources as $item) {
			$data[$item['source_id']] = $item['source_name'];
		}

		return kv($data, $key);
	}
}
