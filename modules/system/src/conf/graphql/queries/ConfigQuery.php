<?php namespace System\Conf\Graphql\Queries;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;


/**
 * Class SettingQuery.
 */
class ConfigQuery extends Query
{
	protected $attributes = [
		'name'        => 'config',
		'description' => 'Config Query.',
	];

	/**
	 * @return ListOfType
	 * @throws TypeNotFound
	 */
	public function type(): ListOfType
	{
		return Type::listOf($this->getGraphQL()->type('conf_item'));
	}

	/**
	 * @return array
	 */
	public function args()
	{
		return [
			'module' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::conf.graphql.conf_module'),
			],
			'group'  => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::conf.graphql.conf_group'),
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
		return $this->getConf()->getNsGroup($args['module'], $args['group']);
	}


}
