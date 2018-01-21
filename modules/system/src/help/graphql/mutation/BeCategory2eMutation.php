<?php namespace System\Help\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use System\Help\Action\Category;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;

class BeCategory2eMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'be_category_2e';
		$this->attributes['description'] = trans('system::category.graphql.mutation_desc');
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
				'type'        => Type::nonNull($this->getGraphQL()->type('InputCategory')),
				'description' => trans('system::category.graphql.item'),
			],
			'id'   => [
				'type'        => Type::int(),
				'description' => trans('system::category.db.id'),
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
		/** @var Category $category */
		$Category = new Category();
		if (!$Category->setPam($this->getJwtBeGuard()->user())->establish($args['item'], $id)) {
			return $Category->getError()->toArray();
		}
		else {
			return $Category->getSuccess()->toArray();
		}
	}
}