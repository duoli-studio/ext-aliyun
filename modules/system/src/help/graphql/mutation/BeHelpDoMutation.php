<?php namespace System\Help\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use System\Help\Action\Help;

class BeHelpDoMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'be_help_do';
		$this->attributes['description'] = trans('system::help.graphql.do_desc');
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
				'type'        => Type::nonNull($this->getGraphQL()->type('BeHelpDoAction')),
				'description' => trans('system::help.graphql.do_type'),
			],
			'id' => [
				'type'        => Type::nonNull(Type::int()),
				'description' => trans('system::help.db.id'),
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

		$Help = (new Help())->setPam($this->getJwtBeGuard()->user());
		if (in_array($args['action'], ['delete',])) {
			$action = camel_case($args['action']);
			if (is_callable([$Help, $action])) {
				if (call_user_func([$Help, $action], $id)) {
					return $Help->getSuccess()->toArray();
				}
				else {
					return $Help->getError()->toArray();
				}
			}
		}
		return (new Resp(Resp::ERROR, '不存在的方法'))->toArray();
	}
}