<?php namespace System\Pam\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use System\Pam\Action\BindChange;
use System\Models\PamAccount;
use System\Pam\Action\Pam;

class RebindPassportMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'rebind_passport';
		$this->attributes['description'] = '[O]' . trans('system::bind_change.graphql.mutation_desc');
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
			'verify_code' => [
				'type'        => Type::string(),
				'description' => trans('system::bind_change.db.verify_code'),
			],
			'passport'    => [
				'type'        => Type::string(),
				'description' => trans('system::bind_change.db.passport'),
			],
			'captcha'     => [
				'type'        => Type::string(),
				'description' => trans('system::bind_change.db.captcha'),
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
		$mobile      = $args['passport'] ?? '';
		$captcha     = $args['captcha'] ?? '';
		$verify_code = $args['verify_code'] ?? '';


		$Pam = (new Pam())->setPam($this->getJwtWebGuard()->user());
		if (!$Pam->rebindPassport($verify_code, $mobile, $captcha)) {
			return $Pam->getError()->toArray();
		}
		else {
			return $Pam->getSuccess()->toArray();
		}
	}
}