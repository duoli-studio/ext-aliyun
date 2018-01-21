<?php namespace System\Setting\Graphql\Queries;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use System\Classes\Traits\SystemTrait;


class BeSettingQuery extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'be_setting';
		$this->attributes['description'] = trans('system::setting.graphql.query_desc');
	}

	/**
	 * @return ObjectType
	 * @throws TypeNotFound
	 */
	public function type(): ObjectType
	{
		return $this->getGraphQL()->type('Setting');
	}

	/**
	 * @return array
	 */
	public function args()
	{
		return [
			'key' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::setting.graphql.key'),
			],
		];
	}

	/**
	 * @param $root
	 * @param $args
	 * @return mixed
	 */
	public function resolve($root, $args)
	{
		$value   = $this->getSetting()->get($args['key']);
		$setting = (array) $this->getModule()->settings()->get($args['key']);
		return [
			'key'         => $args['key'],
			'value'       => $value,
			'description' => data_get($setting, 'description'),
		];
	}


}
