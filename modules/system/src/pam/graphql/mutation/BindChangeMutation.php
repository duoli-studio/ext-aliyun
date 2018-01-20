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
		return $this->getGraphQL()->type('Resp');
	}

	/**
	 * @return array
	 * @throws TypeNotFound
	 */
	public function args(): array
	{
		return [
			'action'      => [
				'type'        => Type::nonNull($this->getGraphQL()->type('BindChange')),
				'description' => trans('system::bind_change.graphql.action_desc'),
			],
			'mobile'      => [
				'type'        => Type::string(),
				'description' => trans('system::bind_change.db.mobile'),
			],
			'captcha'     => [
				'type'        => Type::string(),
				'description' => trans('system::bind_change.db.captcha'),
			],
			'verify_code' => [
				'type'        => Type::string(),
				'description' => trans('system::bind_change.db.verify_code'),
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
		$mobile      = $args['mobile'];
		$captcha     = $args['captcha'];
		$verify_code = $args['verify_code'];

		/** @var PamAccount $pam */
		$pam = $this->getJwtWebGuard()->user();

		/** @var BindChange $bindChange */
		$bindChange = app('act.bind_change');
		$bindChange->setPam($pam);

		switch ($args['action']) {
			case 'old_send':
				if (!$bindChange->oldSendCaptcha($mobile, $captcha)) {
					return $bindChange->getError()->toArray();
				} else {
					return $bindChange->getSuccess()->toArray();
				}
				break;
			case 'old_validate':
				if (!$data = $bindChange->oldValidate($captcha)) {
					return $bindChange->getError()->toArray();
				} else {
					$success = $bindChange->getSuccess()->toArray();
					$success['data'] = $data;
					return $success;
				}
				break;
			case 'new_send':
				if (!$bindChange->newSendCaptcha($verify_code, $mobile)) {
					return $bindChange->getError()->toArray();
				} else {
					return $bindChange->getSuccess()->toArray();
				}
				break;
			case 'new_validate':
				if (!$bindChange->newValidate($mobile, $captcha)) {
					return $bindChange->getError()->toArray();
				} else {
					return $bindChange->getSuccess()->toArray();
				}
				break;
		}
	}
}