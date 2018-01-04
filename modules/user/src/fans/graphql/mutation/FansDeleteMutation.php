<?php namespace User\Fans\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;

class FansDeleteMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'fans_delete';
		$this->attributes['description'] = trans('user::fans.graphql.delete_desc');
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
			'account_id' => [
				'type'        => Type::nonNull(Type::int()),
				'description' => trans('user::fans.db.account_id'),
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
		// todo
		$account_id     = $args['account_id'] ?? 0;
		$fans = app('act.fans');
		$pam = $this->getJwtWebGuard()->user();
		if (!$fans->canceled($account_id,$pam)) {
			return $fans->getError()->toArray();
		}
		else {
			return $fans->getSuccess()->toArray();
		}
	}
}