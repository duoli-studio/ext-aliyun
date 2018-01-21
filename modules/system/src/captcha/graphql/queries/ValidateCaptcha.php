<?php namespace System\Captcha\Graphql\Queries;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use System\Classes\Traits\SystemTrait;
use System\Captcha\Action\Captcha;

class ValidateCaptcha extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'validate_captcha';
		$this->attributes['description'] = '[O]' . trans('system::captcha.graphql.validate_captcha_query');
	}

	/**
	 * @return ObjectType
	 * @throws TypeNotFound
	 */
	public function type(): ObjectType
	{
		return $this->getGraphQL()->type('Resp');
	}

	/**
	 * @return array
	 */
	public function args(): array
	{
		return [
			'passport' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::captcha.graphql.validate_captcha_passport'),
			],
			'captcha'  => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::captcha.graphql.validate_captcha_captcha'),
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
		$passport = $args['passport'] ?? '';
		$captcha  = $args['captcha'] ?? '';

		$actCaptcha = (new Captcha());
		if (!$actCaptcha->check($passport, $captcha)) {
			return $actCaptcha->getError()->toArray();
		}
		else {
			$actCaptcha->delete($passport);
			$success         = $actCaptcha->getSuccess()->toArray();
			$success['data'] = $actCaptcha->genOnceVerifyCode(10, $passport);
			return $success;
		}
	}
}