<?php namespace System\Pam\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use System\Pam\Action\Area;

class AreaMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'area';
		$this->attributes['description'] = trans('system::area.graphql.mutation_desc');
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
			'action' => [
				'type'        => Type::nonNull($this->getGraphQL()->type('Area')),
				'description' => trans('system::area.graphql.action_desc'),
			],
			'id'     => [
				'type'        => Type::int(),
				'description' => trans('system::area.db.id'),
			],
			'data'   => [
				'type'        => Type::string(),
				'description' => trans('system::area.db.data'),
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
		$id   = $args['id'];
		$data = $args['data'];

		/** @var PamAccount $pam */
		$pam = $this->getJwtWebGuard()->user();

		/** @var Area $area */
		$area = app('act.area');
		$area->setPam($pam);

		switch ($args['action']) {
			case 'create':
				if (!$area->create($data)) {
					return $area->getError()->toArray();
				} else {
					return $area->getSuccess()->toArray();
				}
				break;
			case 'edit':
				if (!$area->edit($id, $data)) {
					return $area->getError()->toArray();
				} else {
					return $area->getSuccess()->toArray();
				}
				break;
			case 'destroy':
				if (!$area->destroy($id)) {
					return $area->getError()->toArray();
				} else {
					return $area->getSuccess()->toArray();
				}
				break;
		}
	}
}