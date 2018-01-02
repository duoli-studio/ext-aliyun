<?php namespace System\Addon\GraphQL\Queries;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use System\Classes\Traits\SystemTrait;

/**
 * Class ConfigurationQuery.
 */
class AddonQuery extends Query
{
	use SystemTrait;

	/**
	 * @return array
	 */
	public function args()
	{
		return [
			'enabled'   => [
				'defaultValue' => null,
				'name'         => 'enabled',
				'type'         => Type::boolean(),
			],
			'installed' => [
				'defaultValue' => null,
				'name'         => 'installed',
				'type'         => Type::boolean(),
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
		if ($args['enabled'] === true) {
			return $this->getAddon()->enabled()->toArray();
		}
		elseif ($args['installed'] === true) {
			return $this->getAddon()->installed()->toArray();
		}
		elseif ($args['installed'] === false) {
			return $this->getAddon()->notInstalled()->toArray();
		}

		return $this->getAddon()->repository()->toArray();
	}

	/**
	 * @return ListOfType
	 * @throws TypeNotFound
	 */
	public function type(): ListOfType
	{
		return Type::listOf($this->getGraphQL()->type('addon'));
	}
}
