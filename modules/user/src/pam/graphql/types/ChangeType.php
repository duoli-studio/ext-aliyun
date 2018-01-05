<?php namespace User\Pam\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;

/**
 * Class SettingType.
 */
class ChangeType extends AbstractType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'change';
		$this->attributes['description'] = trans('user::change.graphql.type_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'id' => [
				'type'        => Type::int(),
				'description' => trans('user::change.db.account_id'),
			],
			'nickname'   => [
				'type'        => Type::string(),
				'description' => trans('user::change.db.nickname'),
			],
			'sex'        => [
				'type'        => Type::int(),
				'description' => trans('user::change.db.sex'),
			],
			'signature'  => [
				'type'        => Type::string(),
				'description' => trans('user::change.db.signature'),
			],
			// 游戏名片设置 type类型
			'game'       => [
				'type'        => Type::string(),
				// 'wz_game_nickname'  => [
				// 	'type'        => Type::string(),
				// 	'description' => trans('user::change.game.wz_game_nickname'),
				// ],
				// 'wz_game_system'  => [
				// 	'type'        => Type::string(),
				// 	'description' => trans('user::change.game.wz_game_system'),
				// ],
				// 'wz_game_login_way'  => [
				// 	'type'        => Type::string(),
				// 	'description' => trans('user::change.game.wz_game_login_way'),
				// ],
				// 'wz_good_at_position'  => [
				// 	'type'        => Type::string(),
				// 	'description' => trans('user::change.game.wz_good_at_position'),
				// ],
			],
			'head_pic'   => [
				'type'        => Type::string(),
				'description' => trans('user::change.db.head_pic'),
			],

		];
	}
}
