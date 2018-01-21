<?php namespace System\Area\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Area\Action\Area;
use System\Classes\Traits\SystemTrait;

class BeAreaDoMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'be_area_do';
		$this->attributes['description'] = trans('system::area.graphql.action_desc');
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
				'type'        => Type::nonNull($this->getGraphQL()->type('BeAreaDoAction')),
				'description' => trans('system::area.graphql.action_type'),
			],
			'id'     => [
				'type'        => Type::nonNull(Type::int()),
				'description' => trans('system::area.db.id'),
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

		$Area = (new Area())->setPam($this->getJwtBeGuard()->user());
		if (in_array($args['action'], ['delete'])) {
			$action = camel_case($args['action']);
			if (is_callable([$Area, $action])) {
				if (call_user_func([$Area, $action], $id)) {
					return $Area->getSuccess()->toArray();
				}
				else {
					return $Area->getError()->toArray();
				}
			}
		}
		return (new Resp(Resp::ERROR, '不存在的方法'))->toArray();
	}
}