<?php namespace User\Pam\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use User\Pam\Action\Change;

class ProfileMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'profile';
		$this->attributes['description'] = trans('user::change.graphql.mutation_desc');
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
			// nickname
			// sex
			// description
			'type'  => [
				'type'        => Type::nonNull($this->getGraphQL()->type('profile_change')),
				'description' => trans('user::change.db.account_id'),
			],
			'value' => [
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

		$type     = $args['type'];
		$nickname = $args['value'];

		/** @var PamAccount $pam */
		$pam = $this->getJwtWebGuard()->user();

		/** @var Change $change */
		$profile = app('act.change');
		$profile->setPam($pam);
		//修改方法多个 怎么写
		if (!$profile->nicknameChange($nickname)) {
			return $profile->getError()->toArray();
		}
		else {
			return $profile->getSuccess()->toArray();
		}
	}
}