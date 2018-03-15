<?php namespace System\Setting\Graphql\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;

class BeSettingMutation extends Mutation
{
	use SystemTrait;

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'be_setting';
		$this->attributes['description'] = '[O]' . trans('system::setting.mutation.be_setting.desc');
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
			'key'   => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::setting.mutation.be_setting.arg_key'),
			],
			'value' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::setting.mutation.be_setting.arg_value'),
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
		$key   = $args['key'] ?? '';
		$value = $args['value'] ?? '';
		$conf  = $this->getSetting();
		if (!$conf->set($key, $value)) {
			return $conf->getError()->toArray();
		}
		 
			return $conf->getSuccess()->toArray();
	}
}