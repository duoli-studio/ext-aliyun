<?php namespace System\Pam\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use System\Pam\Action\BindChange;
use System\Models\PamAccount;

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
	 */
	public function args(): array
	{
		return [
			'mobile' => [
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
		$mobile = $args['mobile'];

		/** @var PamAccount $pam */
		$pam = $this->getJwtWebGuard()->user();

		/** @var BindChange $bindChange */
		$bindChange = app('act.bind_change');
		$bindChange->setPam($pam);
		if (!$bindChange->newSendCaptcha($mobile)) {
			return $bindChange->getError()->toArray();
		} else {
			return $bindChange->getSuccess()->toArray();
		}
	}
}