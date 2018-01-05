<?php namespace User\Fans\Graphql\Queries;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use User\Models\UserFans;
use User\Models\UserProfile;


class ConcernQuery extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'concern_list';
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
		return Type::listOf($this->getGraphQL()->type('fans'));
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
		//todo 用户头像 昵称 性别 认证等级
		/** @var PamAccount $pam */
		$pam = $this->getJwtWebGuard()->user();
		/*var_dump($pam->type);*/
		$concern = UserFans::where('fans_id', $pam->id)->pluck('account_id')->toArray();

		foreach ($concern as $id) {
			$Db = UserProfile::where('id', $id)->get();
			foreach ($Db as $v) {
				$users[] = [
					'id'                     => $id,
					'head_pic'               => $v->head_pic,
					'nickname'               => $v->nickname,
					'sex'                    => $v->sex ? '女' : '男',
					'signature'              => $v->signature,
					'lol_is_girl_validated'  => $v->lol_is_girl_validated ? '女猎手' : '',
					'lol_validated_type'     => $v->lol_validated_type,
					'wz_is_girl_validated'   => $v->wz_is_girl_validated ? '女猎手' : '',
					'wz_validated_type'      => $v->wz_validated_type,
					'pubg_is_girl_validated' => $v->pubg_is_girl_validated ? '女猎手' : '',
					'pubg_validated_type'    => $v->pubg_validated_type,
				];
			}
		}
		\Log::debug($users);
		// return UserFans::get();


	}


}
