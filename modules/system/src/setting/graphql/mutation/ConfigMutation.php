<?php namespace System\Setting\GraphQL\Mutation;

use GraphQL\Type\Definition\Type;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;

class ConfigMutation extends Mutation
{
	protected $attributes = [
		'name'        => 'ConfigMutation',
		'description' => 'Modify and set config value',
	];

	/**
	 * @return \GraphQL\Type\Definition\ListOfType|mixed
	 * @throws TypeNotFound
	 */
	public function type()
	{
		return $this->getGraphQL()->type('resp');
	}

	/**
	 * @return array
	 * @throws TypeNotFound
	 */
	public function args(): array
	{
		return [
			'config' => [
				'type'        => Type::nonNull(Type::listOf($this->getGraphQL()->type('config_item'))),
				'description' => trans('system::conf.graphql.item'),
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
		$config = $args['config'] ?? [];
		$conf   = $this->getSetting();
		foreach ($config as $value) {
			if (!$conf->set($value['key'], $value['value'])) {
				return $conf->getError()->toArray();
			}
		}
		return (new Resp(Resp::SUCCESS))->toArray();
	}
}