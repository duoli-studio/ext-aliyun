<?php namespace User\Pam\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use User\Pam\Action\ProfileChange;

class ProfileMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'profile_change';
		$this->attributes['description'] = trans('user::profile.graphql.mutation_desc');
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
				'description' => trans('user::profile.graphql.type_desc'),
			],
			'value' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('user::profile.db.value'),
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

		$type  = $args['type'];
		$value = $args['value'];

		/** @var PamAccount $pam */
		$pam = $this->getJwtWebGuard()->user();

		/** @var ProfileChange $change */
		$profile = app('act.profile_change');
		$profile->setPam($pam);

		if (!$profile->ProfileChange($type, $value)) {
			return $profile->getError()->toArray();
		} else {
			return $profile->getSuccess()->toArray();
		}
	}
}