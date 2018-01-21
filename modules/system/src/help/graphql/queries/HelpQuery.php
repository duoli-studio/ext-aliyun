<?php namespace System\Help\Graphql\Queries;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use System\Classes\Traits\SystemTrait;
use System\Models\Resources\Help;
use System\Models\SysHelp;


class HelpQuery extends Query
{

	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'help';
		$this->attributes['description'] = trans('system::help.graphql.queries_desc');
	}


	public function authorize($root, $args)
	{
		return $this->isJwtUser();
	}

	/**
	 * @return ObjectType
	 * @throws TypeNotFound
	 */
	public function type(): ObjectType
	{
		return $this->getGraphQL()->type('Help');
	}

	/**
	 * @return array
	 */
	public function args()
	{
		return [
			'type' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::help.db.type'),
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
		$type = $args['type'];
		$Db = SysHelp::with('category')->where('type',$type)->first();

		return $Db;

	}
}
