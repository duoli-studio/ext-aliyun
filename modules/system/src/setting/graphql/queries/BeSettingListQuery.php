<?php namespace System\Setting\Graphql\Queries;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use System\Classes\Traits\SystemTrait;


class BeSettingListQuery extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'be_setting_list';
		$this->attributes['description'] = '[O]' . trans('system::setting.query.be_setting_list.desc');
	}

	/**
	 * @return ListOfType
	 * @throws TypeNotFound
	 */
	public function type(): ListOfType
	{
		return Type::listOf($this->getGraphQL()->type('BeSetting'));
	}

	/**
	 * @return array
	 */
	public function args()
	{
		return [
			'namespace' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::setting.query.be_setting_list.arg_namespace'),
			],
			'group'     => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::setting.query.be_setting_list.arg_group'),
			],
		];
	}

	/**
	 * @param $root
	 * @param $args
	 * @return mixed
	 */
	public function resolve($root, $args)
	{
		return $this->getSetting()->getNsGroup($args['namespace'], $args['group']);
	}


}
