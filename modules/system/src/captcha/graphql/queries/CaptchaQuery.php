<?php namespace System\Captcha\Graphql\Queries;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use System\Captcha\Action\Captcha;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use System\Models\PamCaptcha;
use System\Pam\Action\Pam;
use System\Util\Action\ImageCaptcha;

class CaptchaQuery extends Query
{
	use SystemTrait;

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'captcha';
		$this->attributes['description'] = '[O]' . trans('system::captcha.graphql.captcha_query');
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
	 * @throws TypeNotFound
	 */
	public function args(): array
	{
		return [
			'passport'   => [
				'type'        => Type::string(),
				'description' => trans('system::captcha.graphql.captcha_query_passport'),
			],
			'image_code' => [
				'type'        => Type::string(),
				'description' => trans('system::captcha.graphql.captcha_query_image_code'),
			],
			'type'       => [
				'type'        => Type::nonNull($this->getGraphQL()->type('CaptchaAction')),
				'description' => trans('system::captcha.graphql.captcha_action_type'),
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
		$passport   = $args['passport'] ?? '';
		$image_code = $args['image_code'] ?? '';
		$type       = $args['type'] ?? '';


		$passportType = (new Pam())->passportType($passport);

		switch ($type) {
			case PamCaptcha::CON_LOGIN:
				// 登录, 需要手机号, 需要图形验证码
				if (!$passport) {
					return Resp::error('请填入需要发送的通行证手机号');
				}
				if (!$image_code) {
					return Resp::error('请输入图形验证码');
				}

				$actCaptcha = (new ImageCaptcha());
				if (!$actCaptcha->check($passport, $image_code)) {
					return $actCaptcha->getError()->toArray();
				}

				break;
			case PamCaptcha::CON_PASSWORD:

				// 需要验证手机号不为空
				if (!$passport) {
					return Resp::error('请填入需要发送的通行证手机号');
				}

				// 系统中需要存在这个账号
				if (!PamAccount::where($passportType, $passport)->exists()) {
					return Resp::error(trans('system::captcha.action.account_miss'));
				}
				break;

			case PamCaptcha::CON_USER:
				// 需要授权
				$jwtUser = $this->getJwtWebGuard()->user();
				if (!$jwtUser) {
					return Resp::error('你需要登录之后才能发送');
				}
				$passport = $jwtUser->$passportType;
				break;

			case PamCaptcha::CON_REBIND:
				// 需要授权
				$jwtUser = $this->getJwtWebGuard()->user();
				if (!$jwtUser) {
					return Resp::error('你需要登录之后才能发送');
				}

				// 需要验证手机号不为空
				if (!$passport) {
					return Resp::error('请填入需要发送的通行证手机号');
				}

				// 新绑定手机号不能存在
				if (PamAccount::where($passportType, $passport)->exists()) {
					return Resp::error(trans('system::captcha.action.account_exists'));
				}
				break;
		}


		/** @var Captcha $util */
		$util = (new Captcha());
		if (!$util->send($passport, $type)) {
			return $util->getError()->toArray();
		}
		else {
			return $util->getSuccess(
				trans('system::captcha.graphql.send_captcha_success')
			)->toArray();
		}
	}
}