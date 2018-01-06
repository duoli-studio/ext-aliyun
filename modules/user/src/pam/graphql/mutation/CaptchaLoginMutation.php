<?php namespace User\Pam\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use User\Pam\Action\Pam;

class CaptchaLoginMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'captcha_login';
		$this->attributes['description'] = trans('user::login.graphql.mutation_desc');
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
				'description' => trans('user::login.db.passport'),
			],
			'captcha'  => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('user::login.db.captcha'),
			],
		];
	}

	/**
	 * @param $root
	 * @param $args
	 * @return array
	 * @throws \Throwable
	 */
	public function resolve($root, $args)
	{
		$passport = $args['passport'];
		$captcha  = $args['captcha'];
		/** @var Pam $actPam */
		$actPam = app('act.pam');

		if (!$actPam->captchaLogin($passport, $captcha)) {
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
					trans('user::login.graphql.get_token_success')
				)->toArray();
				$success['data'] = $token;
				return $success;
			}
		}
	}
}