<?php namespace System\Area\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Area\Action\Area;
use System\Classes\Traits\SystemTrait;

class BeArea2eMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'be_area_2e';
		$this->attributes['description'] = trans('system::area.graphql.mutation_desc');
	}

	public function authorize($root, $args)
	{
		return $this->isJwtBackend();
	}

	/**
	 * @return ObjectType
	 * @throws TypeNotFound
	 */
	public function type(): ObjectType
	{
		return $this->getGraphQL()->type('Resp');
	}

	/**
	 * @return array
	 * @throws TypeNotFound
	 */
	public function args(): array
	{
		return [
			'item' => [
				'type'        => Type::nonNull($this->getGraphQL()->type('InputBeArea')),
				'description' => trans('system::area.db.item'),
			],
			'id'   => [
				'type'        => Type::int(),
				'description' => trans('system::area.db.id'),
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
		$id = $args['id'] ?? 0;

		$area = new Area();
		if (!$area->setPam($this->getJwtBeGuard()->user())->establish($args['item'], $id)) {
			return $area->getError()->toArray();
		}
		else {
			return $area->getSuccess()->toArray();
		}
	}
}