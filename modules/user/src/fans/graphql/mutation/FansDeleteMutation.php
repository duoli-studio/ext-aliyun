<?php namespace User\Fans\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use User\Fans\Action\Fans;

class FansDeleteMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'fans_delete';
		$this->attributes['description'] = trans('user::fans.graphql.delete_desc');
	}

	public function authorize($root, $args)
	{
		return $this->isJwtUser();
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
	 * @throws \Exception
	 */
	public function resolve($root, $args)
	{
		$account_id     = $args['account_id'] ?? 0;
		/**
		 * @var Fans $fans
		 */
		$fans = app('act.fans');
		if (!$fans->setPam($this->getJwtWebGuard()->user())->canceled($account_id)) {
			return $fans->getError()->toArray();
		}
		else {
			return $fans->getSuccess()->toArray();
		}
	}
}