<?php namespace User\Fans\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use User\Fans\Action\Fans;

class FansMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'fans';
		$this->attributes['description'] = trans('user::fans.graphql.mutation_desc');
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
	 * @throws TypeNotFound
	 */
	public function args(): array
	{
		return [
			'type'       => [
				'type'        => Type::nonNull($this->getGraphQL()->type('fans_handle')),
				'description' => trans('fans::act.db.account_id'),
			],
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
		$account_id = $args['account_id'];
		$type       = $args['type'];
		/** @var Fans $fans */
		$fans = app('act.fans');
		$fans->setPam($this->getJwtWebGuard()->user());
		if ($type == 'attention') {
			if (!$fans->concern($account_id)) {
				return $fans->getError()->toArray();
			}
			else {
				return $fans->getSuccess()->toArray();
			}
		}
		else {
			if (!$fans->cancel($account_id)) {
				return $fans->getError()->toArray();
			}
			else {
				return $fans->getSuccess()->toArray();
			}
		}

	}
}