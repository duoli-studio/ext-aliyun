<?php namespace System\Pam\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use System\Pam\Action\BindChange;

class BindChangeMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'bind_change';
		$this->attributes['description'] = trans('system::bind_change.graphql.mutation_desc');
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
			'account_id' => [
				'type'        => Type::int(),
				'description' => trans('system::bind_change.db.account_id'),
			],
			'mobile'     => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::bind_change.db.mobile'),
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
		$mobile     = $args['mobile'];

		/** @var BindChange $bindChange */
		$bindChange = app('act.bind_change');
		if (!$bindChange->establish($account_id, $mobile)) {
			return $bindChange->getError()->toArray();
		} else {
			return $bindChange->getSuccess()->toArray();
		}
	}
}