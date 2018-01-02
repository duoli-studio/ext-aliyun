<?php namespace Util\Util\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;

class UtilMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'util';
		$this->attributes['description'] = trans('util::act.graphql.mutation_desc');
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
			'passport' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('util::act.db.passport'),
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
		$passport = $args['passport'];
		$util     = app('act.util');
		if (!$util->sendCaptcha($passport)) {
			return $util->getError()->toArray();
		} else {
			return $util->getSuccess()->toArray();
		}
	}
}