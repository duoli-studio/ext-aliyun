<?php namespace System\Help\Graphql\Queries;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use System\Models\SysHelp;
use System\Models\SysCategory;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use System\Classes\Traits\SystemTrait;


class BeHelpQuery extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'be_help';
		$this->attributes['description'] = trans('system::help.graphql.query_desc');
	}


	public function authorize($root, $args)
	{
		return $this->isJwtBackend();
	}

	/**
	 * @return ObjectType
	 * @throws TypeNotFound
	 */
	public function type(): ObjectType
	{
		return $this->getGraphQL()->type('BeHelp');
	}

	/**
	 * @return array
	 */
	public function args()
	{
		return [
			'id' => [
				'type'        => Type::nonNull(Type::int()),
				'description' => trans('system::help.db.id'),
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
		$Db              = SysHelp::find($args['id'])->toArray();
		$Db['cat_title'] = SysCategory::find($Db['cat_id'])->title;
		return $Db;
	}
}
