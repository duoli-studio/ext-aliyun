<?php namespace Slt\Models;

use Carbon\Carbon;
use System\Models\PamAccount;


/**
 * Slt\Models\PrdBook
 * @property int             $id
 * @property string          $title      书籍标题
 * @property int             $account_id User Id
 * @property int             $list_order 显示排序
 * @property bool            $is_favor   是否喜欢
 * @property int             $hits       点击量
 * @property Carbon          $created_at
 * @property Carbon          $updated_at
 * @property-read PamAccount $pam
 * @mixin \Eloquent
 */
class PrdBook extends \Eloquent
{

	protected $table = 'prd_book';

	protected $fillable = [
		'title',
		'account_id',
		'list_order',
		'is_favor',
		'hits',
	];

	public function pam()
	{
		return $this->belongsTo(PamAccount::class, 'account_id', 'id');
	}

}
