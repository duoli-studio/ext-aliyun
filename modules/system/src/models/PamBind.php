<?php namespace System\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * User\Models\Bind
 *
 * @mixin \Eloquent
 * @property int $account_id
 * @property string $qq_key      qq绑定id
 * @property string $qq_union_id qq union id
 * @property string $wx_union_id 微信union
 * @property string $wx_key      微信绑定id
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $created_at
 */
class PamBind extends Model
{
    protected $table = 'pam_bind';

    protected $fillable = [
        'account_id',
        'qq_key',
        'wx_key',
        'qq_union_id',
        'wx_union_id',
    ];
}
