<?php namespace System\Help\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use System\Help\Action\Category;

class BeCategoryDoMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'be_category_do';
		$this->attributes['description'] = trans('system::category.graphql.do_desc');
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
			'action' => [
				'type'        => Type::nonNull($this->getGraphQL()->type('BeCategoryDoAction')),
				'description' => trans('system::category.graphql.do_type'),
			],
			'id' => [
				'type'        => Type::nonNull(Type::int()),
				'description' => trans('system::category.db.id'),
			],
		];
	}

	/**
	 * @param $root
	 * @param $args
	 * @return array
	 * @throws \Exception
	 */
	public function resolve($root, $args)
	{
		$id = $args['id'] ?? 0;
		$Category = (new Category())->setPam($this->getJwtBeGuard()->user());
		if (in_array($args['action'], ['delete',])) {
			$action = camel_case($args['action']);
			if (is_callable([$Category, $action])) {
				if (call_user_func([$Category, $action], $id)) {
					return $Category->getSuccess()->toArray();
				}
				else {
					return $Category->getError()->toArray();
				}
			}
		}
		return (new Resp(Resp::ERROR, '不存在的方法'))->toArray();
	}
}