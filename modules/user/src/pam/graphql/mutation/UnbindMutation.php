<?php namespace User\Pam\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use User\Fans\Action\Fans;
use User\Pam\Action\User;

class UnbindMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'unbind';
		$this->attributes['description'] = trans('user::act.graphql.mutation_desc');
	}

	public function authorize($root, $args)
	{
		// true, if logged in
		return !$this->getJwtBeGuard()->guest();
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
			'type' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('user::act.db.type'),
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
		$account_id = $args['type'];
		/** @var User $user**/
		$user     = app('act.user');
		$user->setPam($this->getJwtWebGuard()->user());
		if (!$user->unbind($account_id)) {
			return $user->getError()->toArray();
		} else {
			return $user->getSuccess()->toArray();
		}
	}
}