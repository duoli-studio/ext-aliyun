<?php namespace User\Pam\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use User\Pam\Action\Change;

class ChangeMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'change';
		$this->attributes['description'] = trans('user::change.graphql.mutation_desc');
	}


	/**
	 * @return ObjectType
	 * @throws TypeNotFound
	 */
	public function type(): ObjectType
	{
		return $this->getGraphQL()->type('resp');
	}

	/**
	 * @return array
	 */
	public function args(): array
	{
		return [
			'id' => [
				'type'        => Type::nonNull(Type::int()),
				'description' => trans('user::change.db.account_id'),
			],
			'nickname'   => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('user::change.db.nickname'),
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
		$account_id = $args['id'];
		$nickname   = $args['nickname'];
		/** @var Change $change */
		$change     = app('act.change');

		//修改方法多个 怎么写
		if (!$change->nicknameChange($account_id, $nickname)) {
			return $change->getError()->toArray();
		} else {
			return $change->getSuccess()->toArray();
		}
	}
}