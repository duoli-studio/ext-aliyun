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
	protected $attributes = [
		'name'        => 'resp',
		'description' => 'Response message',
	];

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'status'  => [
				'type'         => Type::nonNull(Type::int()),
				'description'  => trans('system::conf.graphql.resp_status'),
				'defaultValue' => Resp::SUCCESS,
			],
			'message' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::conf.graphql.resp_message'),
			],
		];
	}

}
