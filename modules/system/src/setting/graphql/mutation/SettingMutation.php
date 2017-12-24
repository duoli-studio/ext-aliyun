<?php namespace System\Setting\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;

class SettingMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'setting';
		$this->attributes['description'] = trans('system::setting.graphql.mutation_desc');
	}

	/**
	 * @return ObjectType
	 * @throws TypeNotFound
	 */
	public function type(): ObjectType
	{
		return $this->getGraphQL()->type('setting');
	}

	/**
	 * @return array
	 */
	public function args(): array
	{
		return [
			'key'   => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::setting.graphql.key'),
			],
			'value' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::setting.graphql.value'),
			],
		];
	}

	/**
	 * @param $root
	 * @param $args
	 * @return array
	 */
	public function resolve($root, $args)
	{
		$key   = $args['key'] ?? '';
		$value = $args['value'] ?? '';
		$conf  = $this->getSetting();
		if (!$conf->set($key, $value)) {
			return $conf->getError()->toArray();
		}
		else {
			return [
				'key'         => $key,
				'value'       => $conf->get($key),
				'description' => '',
			];
		}
	}
}