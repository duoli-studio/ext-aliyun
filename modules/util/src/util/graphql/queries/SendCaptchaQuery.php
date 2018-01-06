<?php namespace Util\Util\Graphql\Queries;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use System\Classes\Traits\SystemTrait;

class SendCaptchaQuery extends Query
{
	use SystemTrait;

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'send_captcha';
		$this->attributes['description'] = trans('util::util.graphql.send_captcha_queries_desc');
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
			'passport' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('util::util.db.passport'),
			],
			'type'     => [
				'type'        => Type::nonNull($this->getGraphQL()->type('send_captcha_type')),
				'description' => trans('util::util.graphql.send_captcha_type_desc'),
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
		$type     = $args['type'];

		$util = app('act.util');
		if (!$util->sendCaptcha($passport, $type)) {
			return $util->getError()->toArray();
		}
		else {
			return $util->getSuccess(
				trans('util::util.graphql.send_captcha_success')
			)->toArray();
		}
	}
}