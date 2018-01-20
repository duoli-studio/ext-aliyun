<?php namespace System\Setting\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;

/**
 * Class SettingType.
 */
class RespType extends AbstractType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'Resp';
		$this->attributes['description'] = trans('system::act.graphql.resp_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'status'  => [
				'type'         => Type::nonNull(Type::int()),
				'description'  => trans('system::act.graphql.resp_status'),
				'defaultValue' => Resp::SUCCESS,
			],
			'message' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::act.graphql.resp_message'),
			],
			'data'    => [
				'type'        => Type::string(),
				// todo 注释
				'description' => trans('system::act.graphql.resp_message'),
			],
		];
	}

}
