<?php namespace User\Models;


/**
 * User\Models\UserProfile
 *
 * @property int         $id                    账户id
 * @property string      $nickname              昵称（备注）
 * @property int         $sex                   性别 0：男 1：女
 * @property string      $signature             个性签名
 * @property string      $mobile                手机号
 * @property string      $qq                    qq
 * @property int         $area_id               地区id
 * @property string      $address               地址
 * @property string      $head_pic              头像
 * @property float       $money                 资金
 * @property int         $is_chid_validated     实名认证的状态 0: 未认证, 1: 已经认证
 * @property int         $lol_is_girl_validated lol是否认证过女猎手
 * @property int         $wz_is_girl_validated
 * @property int         $pubg_is_girl_validated
 * @property int         $lol_is_validated      lol是否认证过猎手
 * @property int         $lol_validated_type    LOL认证猎手级别 0：普通1：优质 2：金牌
 * @property int         $wz_is_validated
 * @property int         $wz_validated_type
 * @property int         $pubg_is_validated
 * @property int         $pubg_validated_type
 * @property string      $truename              真实姓名
 * @property string      $personal_pic          个人形象主照片
 * @property int         $punish_point          惩戒值/惩戒点数
 * @property int         $punish_num            当天惩戒次数
 * @property float       $star                  对猎手的综合评分
 * @property int         $order_listening       听单状态  0: No, 1:正在听单
 * @property int         $friend_num            好友数量
 * @property int         $fans_num              粉丝数量
 * @property int         $follow_num            关注数量
 * @property string      $wz_game_nickname      游戏昵称
 * @property string      $wz_game_system        手机系统
 * @property string      $wz_game_login_way     登录方式
 * @property string      $wz_good_at_position   擅长位置
 * @property string      $wz_game_hero          本命英雄
 * @property string      $lol_game_nickname     游戏昵称
 * @property string      $lol_game_service      游戏区服
 * @property string      $lol_good_at_position  擅长位置
 * @property string      $lol_game_hero         本命英雄
 * @property string      $pubg_game_nickname    游戏昵称
 * @property string      $pubg_game_steamid     Steam id
 * @property string      $pubg_game_rank        赛季综合评分
 * @property int         $account_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @mixin \Eloquent
 */
class UserProfile extends \Eloquent
{


	protected $table = 'user_profile';


	public $timestamps = false;

	protected $fillable = [
		'id',
		'nickname',
		'sex',
		'signature',
		'mobile',
		'qq',
		'is_chid_validated',
		'lol_is_girl_validated',
		'wz_is_girl_validated',
		'pubg_is_girl_validated',
		'lol_is_validated',
		'lol_validated_type',
		'wz_is_validated',
		'wz_validated_type',
		'pubg_is_validated',
		'pubg_validated_type',
		'area_id',
		'address',
		'head_pic',
		'money',
		'truename',
		'personal_pic',
		'punish_point',
		'punish_num',
		'star',
		'order_listening',
		'friend_num',
		'fans_num',
		'follow_num',
		'wz_game_nickname',
		'wz_game_system',
		'wz_game_login_way',
		'wz_good_at_position',
		'wz_game_hero',
		'lol_game_nickname',
		'lol_game_service',
		'lol_good_at_position',
		'lol_game_hero',
		'pubg_game_nickname',
		'pubg_game_steamid',
		'pubg_game_rank',

	];
}
