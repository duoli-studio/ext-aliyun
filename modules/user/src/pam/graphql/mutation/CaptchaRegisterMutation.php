<?php namespace User\Pam\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use User\Pam\Action\Pam;

class CaptchaRegisterMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'captcha_register';
		$this->attributes['description'] = trans('user::captcha_register.graphql.mutation_desc');
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
			'passport' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('user::captcha_register.db.passport'),
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
		/** @var Pam $pam */
		$pam     = app('act.pam');
		if (!$pam->register($passport)) {
			return $pam->getError()->toArray();
		} else {
			return $pam->getSuccess()->toArray();
		}
	}
}