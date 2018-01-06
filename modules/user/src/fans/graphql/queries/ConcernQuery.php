<?php namespace User\Fans\Graphql\Queries;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use User\Models\Resources\ConcernResource;
use User\Models\UserFans;
use User\Models\UserProfile;


class ConcernQuery extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'concern';
		$this->attributes['description'] = trans('user::fans.graphql.queries_desc');
	}


	public function authorize($root, $args)
	{
		return $this->isJwtUser();
	}

	/**
	 * @return ListOfType
	 * @throws TypeNotFound
	 */
	public function type(): ListOfType
	{
		return Type::listOf($this->getGraphQL()->type('list'));
	}

	/**
	 * @return array
	 */
	public function args()
	{
		return [];
	}

	/**
	 * @param $root
	 * @param $args
	 * @return mixed
	 */
	public function resolve($root, $args)
	{
		//todo  分页
		/** @var PamAccount $pam */
		$pam = $this->getJwtWebGuard()->user();
		$concern = UserFans::where('fans_id', $pam->id)->pluck('account_id')->toArray();

		$users = [];
		foreach ($concern as $id) {
			$Db = UserProfile::where('account_id', $id)->get();
			foreach ($Db as $v) {
				$users[] = (new ConcernResource($v))->toArray($this->getRequest());
			}
		}
		//todo
		// $users['num'] = count($concern);
		\Log::debug($users);
		return $users;
	}
}
