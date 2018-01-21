<?php namespace System\Pam\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use System\Pam\Action\Pam;

class BePamDisableMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'be_pam_disable';
		$this->attributes['description'] = trans('system::account.action.disable');
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
		return $this->getGraphQL()->type('Resp');
	}

	/**
	 * @return array
	 */
	public function args(): array
	{
		return [
			'id'   => [
				'type'        => Type::nonNull(Type::int()),
				'description' => trans('system::account.db.id'),
			],
			'data' => [
				'type'        => Type::string(),
				'description' => trans('system::account.action.disable_data'),
			],
		];
	}

	/**
	 * 暂时只是支持 delete
	 * 其他功能等到用到的时候再加上
	 * @param $root
	 * @param $args
	 * @return array
	 * @throws \Exception
	 */
	public function resolve($root, $args)
	{
		$id   = $args['id'];
		$data = $args['data'] ?? '';

		$account = (new Pam())->setPam($this->getJwtBeGuard()->user());
		if (!$account->disable($id, $data)) {
			return $account->getError()->toArray();
		}
		else {
			return $account->getSuccess()->toArray();
		}
	}
}