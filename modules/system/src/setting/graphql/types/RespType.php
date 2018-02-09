<?php namespace System\Setting\Graphql\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;


class RespType extends AbstractType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'Resp';
		$this->attributes['description'] = trans('system::setting.type.resp.desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'status'  => [
				'type'         => Type::nonNull(Type::int()),
				'description'  => trans('system::setting.type.resp.field_status'),
				'defaultValue' => Resp::SUCCESS,
			],
			'message' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::setting.type.resp.field_message'),
			],
			'data'    => [
				'type'        => Type::string(),
				// todo 注释
				'description' => trans('system::setting.type.resp.field_message'),
			],
		];
	}

}
