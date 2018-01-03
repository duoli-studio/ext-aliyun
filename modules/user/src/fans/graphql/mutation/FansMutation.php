<?php namespace User\Fans\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;

class FansMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'fans';
		$this->attributes['description'] = trans('fans::act.graphql.mutation_desc');
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
			'account_id' => [
				'type'        => Type::nonNull(Type::int()),
				'description' => trans('fans::act.db.fans_id'),
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
		$account_id = $args['account_id'];
		$fans     = app('act.fans');
		$pam = $this->getJwtWebGuard()->user();
		if (!$fans->concern($account_id,$pam)) {
			return $fans->getError()->toArray();
		} else {
			return $fans->getSuccess()->toArray();
		}
	}
}