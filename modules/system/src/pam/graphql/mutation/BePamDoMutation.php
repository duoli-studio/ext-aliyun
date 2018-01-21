<?php namespace System\Pam\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use System\Pam\Action\Pam;

class BePamDoMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'be_pam_do';
		$this->attributes['description'] = trans('system::account.graphql.mutation_desc');
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
				'type'        => Type::nonNull($this->getGraphQL()->type('BePamAction')),
				'description' => trans('system::account.graphql.action_desc'),
			],
			'id'     => [
				'type'        => Type::nonNull(Type::int()),
				'description' => trans('system::account.db.id'),
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
		$id = $args['id'];

		$account = (new Pam())->setPam($this->getJwtBeGuard()->user());
		switch ($args['action']) {
			case 'enable':
				if (!$account->enable($id)) {
					return $account->getError()->toArray();
				}
				else {
					return $account->getSuccess()->toArray();
				}
				break;
		}

	}
}