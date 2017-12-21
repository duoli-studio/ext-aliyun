<?php namespace App\Models;


/**
 * App\Models\GameServer
 * @property integer                   $server_id            分类id
 * @property string                    $server_title         服务器
 * @property integer                   $game_id              游戏id
 * @property integer                   $parent_id            父id
 * @property string                    $parent_ids
 * @property string                    $has_child
 * @property string                    $children
 * @property string                    $is_enable            服务器是否可用
 * @property integer                   $list_order           服务器排序
 * @property \Carbon\Carbon            $created_at
 * @property string                    $deleted_at
 * @property \Carbon\Carbon            $updated_at
 * @property string                    $plat_dailianmao      代练猫
 * @property string                    $plat_dailiantong     代练通
 * @property string                    $plat_yidailian       易代练
 * @property string                    $plat_dianjingbaozi   电竞包子
 * @property string                    $plat_tencent         腾讯编码
 * @property string                    $plat_yqdailian       17代练编码
 * @property-read \App\Models\GameName $game
 * @mixin \Eloquent
 */
class GameServer extends \Eloquent
{

	protected $table = 'game_server';

	protected $primaryKey = 'server_id';

	public $timestamps = true;

	protected $fillable = [
		'game_id',
		'parent_id',
		'server_title',
		'is_enable',
		'plat_dailianmao',
		'plat_dailiantong',
		'plat_yidailian',
		'plat_dianjingbaozi',
		'plat_tencent',
		'yq_game_id',
		'plat_yqdailian',
	];

	public function game()
	{
		return $this->belongsTo('App\Models\GameName', 'game_id');
	}

	public function scopeEnable($query)
	{
		return $query->where('is_enable', 'Y');
	}

	public function scopeDisable($query)
	{
		return $query->where('is_enable', 'N');
	}


	/**
	 * 获取游戏旗下的所有服务器列表
	 * @param      $game_id
	 * @param bool $tree
	 * @return mixed
	 */
	public static function getAll($game_id, $tree = false)
	{
		$servers = self::where('is_enable', 'Y')->where('game_id', $game_id)->get();
		if ($tree) {
			$arr = [];
			foreach ($servers as $key => $server) {
				$server['pid']      = $server['parent_id'];
				$server['id']       = $server['server_id'];
				$server['title']    = $server['server_title'];
				$arr[$server['id']] = $server;
			}
			return $arr;
		} else {
			return $servers;
		}
	}


	/**
	 * 服务级联的名称
	 * @param     $server_id
	 * @param int $level
	 * @return mixed|string
	 */
	public static function getLinkageName($server_id, $level = 0)
	{
		$server = GameServer::find($server_id);
		if ($server['parent_id'] != 0) {
			$level += 1;
			return self::getLinkageName($server['parent_id'], $level) . '/' . $server['server_title'];
		} else {
			return $server['server_title'];
		}
	}

	/**
	 * 检测给定的服务器id 是否是父ID
	 * @param $server_id
	 * @return bool
	 */
	public static function isTop($server_id)
	{
		return !GameServer::find($server_id)->parent_id;
	}

	/**
	 * 获取代练通服务器编码
	 * @param $server_id
	 * @return string
	 */
	public static function tongServerCode($server_id)
	{
		return self::find($server_id)->plat_dailiantong;
	}

	/**
	 * 获取易代练服务器编码
	 * @param $server_id
	 * @return string
	 */
	public static function yiServerCode($server_id)
	{
		return self::find($server_id)->plat_yidailian;
	}


	/**
	 * 获取电竞包子服务器编码
	 * @param $server_id
	 * @return string
	 */
	public static function baoziServerCode($server_id)
	{
		return self::find($server_id)->plat_dianjingbaozi;
	}
	/**
	 * 获取17代练服务器编码
	 * @param $server_id
	 * @return string
	 */
	public static function yqServerCode($server_id)
	{
		return self::find($server_id)->plat_yqdailian;
	}

}
