<?php namespace User\Pam\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use User\Pam\Action\Pam;
use Poppy\Framework\Classes\Resp;
use System\Models\PamAccount;

class PasswordLoginMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'password_login';
		$this->attributes['description'] = trans('user::password_login.graphql.mutation_desc');
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
				'description' => trans('user::password_login.db.passport'),
			],
			'password' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('user::password_login.db.password'),
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
		$password = $args['password'];
		/** @var Pam $actPam */
		$actPam = app('act.pam');

		// if (!$actPam->loginCheck($passport, $password, PamAccount::GUARD_JWT_WEB)) {
		if (!$actPam->loginCheck($passport, $password)) {
			$error         = $actPam->getError()->toArray();
			$error['data'] = '';
			return $error;
		}
		else {
			$pam = $actPam->getPam();

			if (!$token = $this->getJwt()->fromUser($pam)) {
				$error         = (new Resp(Resp::ERROR, '获取凭证失败'))->toArray();
				$error['data'] = '';
				return $error;
			}
			else {
				$success         = $actPam->getSuccess(
					trans('user::password_login.graphql.get_token_success')
				)->toArray();
				$success['data'] = $token;
				return $success;
			}
		}
	}
}