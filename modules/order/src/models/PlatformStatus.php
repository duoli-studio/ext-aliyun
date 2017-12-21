<?php namespace App\Models;


/**
 * App\Models\PlatformStatus
 * php artisan ide-helper:models "App\Models\PlatformStatus"
 *
 * @property integer              $id
 * @property integer              $order_id
 * @property boolean              $tong_is_delete                        代练通删除标识
 * @property string               $tong_order_no                         代练通订单
 * @property string               $tong_order_price                      代练通价格
 * @property integer              $tong_order_hour                       代练时限（小时）
 * @property integer              $tong_stamp                            时间戳
 * @property string               $tong_created_uid                      发布者用户ID
 * @property string               $tong_created_at                       发布时间
 * @property string               $tong_updated_at                       更新时间
 * @property integer              $tong_is_pub                           是否公共频道
 * @property string               $tong_created_username                 发布者用户昵称
 * @property string               $tong_contact_name                     发单联系人
 * @property string               $tong_label1                           标签1
 * @property string               $tong_label2                           标签2
 * @property string               $tong_memo                             备忘
 * @property string               $tong_sd_uid                           接手者用户ID
 * @property string               $tong_sd_username                      接手者昵称
 * @property string               $tong_sd_qq                            接手者QQ
 * @property string               $tong_sd_mobile                        接手者手机
 * @property string               $tong_cancel_note                      撤单相关文字描述
 * @property integer              $tong_left_hour                        剩余时间(小时)
 * @property integer              $tong_order_status                     订单状态
 * @property integer              $tong_cancel_status                    撤销状态
 * @property boolean              $tong_is_lock                          是否锁定
 * @property boolean              $tong_is_star                          是否已评价(0 未评价 1 已评价)
 * @property string               $tong_started_at                       接手时间
 * @property string               $tong_ended_at                         结束时间
 * @property boolean              $mao_is_delete                         代练猫删除标识
 * @property tinyint              $mao_is_lock                           代练猫是否锁定
 * @property tinyint              $mao_is_kf                             代练猫是否申请客服介入
 * @property string               $mao_sd_qq                             接单人qq
 * @property string               $mao_sd_uid                            接单人ID
 * @property string               $mao_sd_name                           接单人昵称
 * @property string               $mao_sd_mobile                         接单人联系电话
 * @property string               $mao_status_desc                       状态名称
 * @property integer              $mao_order_hour                        要求时间，单位小时
 * @property integer              $mao_left_hour                         剩余时间(已经接手的订单才有)，单位小时
 * @property string               $mao_started_at                        接单时间
 * @property boolean              $yi_is_delete                          易代练删除
 * @property boolean              $tong_is_publish                       代练通是否发布成功
 * @property string               $tong_pt_message                       平台提示
 * @property boolean              $mao_is_publish                        代练猫发布标识
 * @property string               $mao_pt_message                        平台提示信息
 * @property boolean              $yi_is_publish                         易代练是否发布成功
 * @property boolean              $yi_pt_message                         易代练平台信息
 * @property boolean              $yi_order_no                           易代练订单号
 * @property boolean              $tong_is_error                         代练通是否有错误信息
 * @property boolean              $mao_is_error                          代练猫是否有错误
 * @property boolean              $yi_is_error                           是否有错误信息
 * @property string               $mao_order_no                          代练猫订单号
 * @property integer              $pt_account_id                         平台发单账号ID
 * @property integer              $pt_account_note                       平台发单账号备注
 * @property string               $platform                              平台
 * @property boolean              $mao_can_modify                        是否能够修改
 * @property string               $mao_type_title                        代练标题
 * @property string               $mao_sell_id                           ?
 * @property string               $mao_game_id                           游戏ID标识
 * @property integer              $mao_order_status                      订单状态
 * @property integer              $mao_cancel_status                     撤销状态
 * @property integer              $mao_upload_result                     上传结果
 * @property integer              $mao_review_status                     是否评论 ?
 * @property integer              $mao_close_day                         关闭天数
 * @property string               $mao_created_at
 * @property string               $mao_updated_at
 * @property string               $mao_order_price
 * @property \Carbon\Carbon       $created_at
 * @property \Carbon\Carbon       $updated_at
 * @property string               $deleted_at
 * @property string               $sync_at                               同步时间
 * @property boolean              $tong_is_accept                        代练通接单
 * @property string               $mao_is_accept                         代练猫接单
 * @property boolean              $yi_is_accept                          易代练已经接单
 * @property boolean              $tong_is_over                          代练通是否完成订单
 * @property boolean              $mao_is_over                           代练猫是否完成订单
 * @property boolean              $yi_is_over                            是否完成订单
 * @property-read PlatformOrder   $platformOrder
 * @property-read PlatformAccount $platformAccount
 * @mixin \Eloquent
 * @property string               $yi_assigned_at                        易代练接单时间
 * @property string               $accepted_at                           接单时间
 * @property string               $yi_order_status                       易代练订单状态
 * @property float                $yi_order_add_price                    奖金
 * @property integer              $yi_is_lock                            订单是否锁定
 * @property integer              $yi_is_exception                       是否有异常
 * @property integer              $yi_is_star                            是否评价
 * @property string               $yi_cancel_type                        取消/撤销类型
 * @property string               $yi_kf_status                          客服状态
 * @property string               $yi_cancel_status                      取消状态
 * @property integer              $yi_is_cancel                          是否取消
 * @property string               $yi_exception_status                   异常状态
 * @property string               $yi_exception_type                     异常状态类型
 * @property integer              $yi_is_public                          易代练是否是公共
 * @property integer              $yi_sd_uid                             易代练用户id
 * @property string               $yi_sd_username                        易代练用户名
 * @property string               $yi_sd_mobile                          易代练手机号
 * @property string               $yi_sd_qq                              代练员qq
 * @property string               $yi_sd_contact                         代练员联系方式
 * @property float                $yi_pub_pay                            发单者赔付
 * @property float                $yi_sd_pay                             代练员赔付
 * @property string               $yi_created_at                         创建时间
 * @property string               $yi_overed_at                          订单完成时间
 * @property string               $yi_ended_at                           订单结束时间
 * @property integer              $yi_pub_uid                            发单者Uid
 * @property string               $yi_pub_username                       发单者username
 * @property string               $yi_pub_mobile                         发单者手机
 * @property string               $yi_pub_contact                        发单者联系人方式
 * @property string               $yi_pub_qq                             发单者qq
 * @property string               $yi_updated_at                         更新时间
 * @property integer              $yi_left_hour                          剩余时间(小时)
 * @property float                $yi_order_price                        易代练价格
 * @property integer              $yi_order_hour                         代练时限(小时)
 * @property boolean              $mama_is_delete                        代练妈妈删除标识
 * @property tinyint              $mama_is_lock                          代练妈妈是否锁定
 * @property tinyint              $mama_is_kf                            代练妈妈是否申请客服介入
 * @property string               $mama_sd_qq                            接单人qq
 * @property string               $mama_sd_uid                           接单人ID
 * @property string               $mama_sd_name                          接单人昵称
 * @property string               $mama_sd_mobile                        接单人联系电话
 * @property string               $mama_status_desc                      状态名称
 * @property integer              $mama_order_hour                       要求时间，单位小时
 * @property integer              $mama_left_hour                        剩余时间(已经接手的订单才有)，单位小时
 * @property string               $mama_started_at                       接单时间
 * @property boolean              $mama_is_publish                       代练妈妈发布标识
 * @property string               $mama_pt_message                       平台提示信息
 * @property int                  $employee_id                           发送到的员工id
 * @property string               $employee_order_status                 员工接单的订单状态
 * @property string               $employee_cancel_status                员工订单取消状态
 * @property string               $employee_cancel_type                  员工订单撤销类型
 * @property string               $mama_order_no                         代练妈妈订单号
 * @property bool                 $mama_is_accept                        代练妈妈接单
 * @property bool                 $mama_is_error                         代练妈妈是否有错误
 * @property bool                 $mama_is_over                          代练妈妈是否完成订单
 * @property bool                 $mama_can_modify                       是否能够修改
 * @property string               $mama_type_title                       代练标题
 * @property string               $mama_sell_id                          ?
 * @property string               $mama_game_id                          游戏ID标识
 * @property int                  $mama_order_status                     订单状态
 * @property int                  $mama_cancel_status                    代练妈妈撤销状态
 * @property int                  $mama_upload_result                    上传结果
 * @property int                  $mama_review_status                    是否评论 ?
 * @property int                  $mama_close_day                        关闭天数
 * @property string               $mama_created_at
 * @property string               $mama_updated_at
 * @property float                $mama_order_price
 * @property string               $yq_order_no                           订单号
 * @property float                $yq_order_price                        订单价格
 * @property int                  $yq_order_type                         订单类型
 * @property int                  $yq_order_status                       订单状态
 * @property string               $yq_order_title                        订单标题
 * @property datetime             $yq_order_at                           订单录入时间
 * @property datetime             $yq_order_create_at                    订单创建时间
 * @property float                $yq_order_hour                         订单要求完成时间
 * @property string               $yq_contact_tel                        号主联系方式
 * @property string               $yq_contact_name                       游戏号主名字
 * @property int                  $yq_contact_id                         游戏号主id
 * @property string               $yq_game_account                       游戏账号
 * @property string               $yq_game_password                      游戏密码
 * @property string               $yq_game_nickname                      游戏昵称
 * @property datetime             $yq_update_at                          修改时间
 * @property decimal              $yq_order_overflow
 * @property int                  $yq_publishing_platform                发单平台设定 公共区和优质区
 * @property string               $yq_game_part                          游戏区服
 * @property string               $yq_sd_name                            接单者名称
 * @property string               $yq_sd_mobile                          接单者手机号
 * @property string               $yq_sd_qq                              接单者qq号
 * @property int                  $yq_sd_id                              接单者id
 * @property tinyint              $yq_is_delete                          是否删除订单
 * @property tinyint              $yq_is_publish                         是否发布成功
 * @property tinyint              $yq_is_error                           是否有错误信息
 * @property string               $yq_pt_message                         平台信息提示
 * @property tinyint              $yq_is_over                            订单是否完成
 * @property datetime             $yq_assigned_at                        接单时间
 * @property tinyint              $yq_is_accept                          代练接单
 * @property int                  $yq_left_hour                          剩余时间
 * @property datetime             $yq_ended_at                           订单结束时间
 * @property int                  $yq_is_exception                       是否有异常
 * @property string               $yq_cancel_status                      取消状态
 * @property string               $yq_cancel_type                        取消/撤销类型
 * @property string               $yq_kf_status                          客服状态
 * @property string               $yq_overed_at                          订单完成时间
 * @property int                  $yq_is_lock                            订单是否锁定
 * @property int                  $yq_is_star                            是否评价
 * @property int                  $yq_pub_uid                            发单者uid
 * @property string               $yq_pub_name                           发单者姓名
 * @property string               $yq_pub_mobile                         发单者手机号
 * @property string               $yq_pub_contact                        发单者联系方式
 * @property string               $yq_exception_status                   异常状态
 * @property string               $yq_exception_type                     异常状态类型
 * @property decimal              $yq_pub_pay                            发单者赔付
 * @property decimal              $yq_sd_pay                             代练者赔付
 * @property datetime             $yq_created_at                         创建时间
 * @property decimal              $yq_add_price                          加价
 * @property decimal              $yq_speed_money                        效率保证金
 * @property decimal              $yq_safe_money                         安全保证金
 * @property datetime             $yq_level_time                         剩余时间
 * @property string               $yq_pub_qq                             发布者qq
 * @property datetime             $yq_receiver_at                        订单完成时间
 * @property string               $yq_started_at                         接单时间
 *
 * @property boolean              $baozi_is_publish                      易代练是否发布成功
 * @property boolean              $baozi_pt_message                      易代练平台信息
 * @property boolean              $baozi_order_no                        易代练订单号
 * @property string               $baozi_assigned_at                     电竞包子接单时间
 * @property string               $baozi_order_status                    电竞包子订单状态
 * @property float                $baozi_order_add_price                 奖金
 * @property boolean              $baozi_is_accept                       电竞包子已经接单
 * @property boolean              $baozi_is_error                        是否有错误信息
 * @property boolean              $baozi_is_over                         是否完成订单
 * @property boolean              $baozi_is_delete                       电竞包子删除
 * @property integer              $baozi_is_lock                         订单是否锁定
 * @property integer              $baozi_is_exception                    是否有异常
 * @property integer              $baozi_is_star                         是否评价
 * @property string               $baozi_cancel_type                     取消/撤销类型
 * @property string               $baozi_kf_status                       客服状态
 * @property string               $baozi_cancel_status                   取消状态
 * @property integer              $baozi_is_cancel                       是否取消
 * @property string               $baozi_exception_status                异常状态
 * @property string               $baozi_exception_type                  异常状态类型
 * @property integer              $baozi_is_public                       电竞包子是否是公共
 * @property integer              $baozi_sd_uid                          电竞包子用户id
 * @property string               $baozi_sd_username                     电竞包子用户名
 * @property string               $baozi_sd_mobile                       电竞包子手机号
 * @property string               $baozi_sd_qq                           代练员qq
 * @property string               $baozi_sd_contact                      代练员联系方式
 * @property float                $baozi_pub_pay                         发单者赔付
 * @property float                $baozi_sd_pay                          代练员赔付
 * @property string               $baozi_created_at                      创建时间
 * @property string               $baozi_overed_at                       订单完成时间
 * @property string               $baozi_ended_at                        订单结束时间
 * @property integer              $baozi_pub_uid                         发单者Uid
 * @property string               $baozi_pub_username                    发单者username
 * @property string               $baozi_pub_mobile                      发单者手机
 * @property string               $baozi_pub_contact                     发单者联系人方式
 * @property string               $baozi_pub_qq                          发单者qq
 * @property string               $baozi_updated_at                      更新时间
 * @property integer              $baozi_left_hour                       剩余时间(小时)
 * @property float                $baozi_order_price                     电竞包子价格
 * @property integer              $baozi_order_hour                      代练时限(小时)
 */
class PlatformStatus extends \Eloquent
{

	const IS_PUBLISH_YES = 1;
	const IS_PUBLISH_NO  = 0;

	const IS_DELETE_YES = 1;
	const IS_DELETE_NO  = 0;

	protected $table = 'platform_status';

	protected $primaryKey = 'id';

	protected $fillable = [
		'order_id',
		'platform',
		'pt_account_id',
		'mao_order_price',
		'employee_id',
		'employee_order_status',
		'employee_cancel_status',
	];

	public function platformOrder()
	{
		return $this->belongsTo('App\Models\PlatformOrder', 'order_id', 'order_id');
	}

	public function platformAccount()
	{
		return $this->belongsTo('App\Models\PlatformAccount', 'pt_account_id', 'id');
	}

	/**
	 * @param null $key
	 * @return array|mixed|string
	 */
	public static function kvIsPublish($key = null)
	{
		$desc = [
			self::IS_PUBLISH_YES => '已经发单',
			self::IS_PUBLISH_NO  => '发单失败',
		];
		return kv($desc, $key);
	}

	/**
	 * @param null $key
	 * @return array|mixed|string
	 */
	public static function kvIsDelete($key = null)
	{
		$desc = [
			self::IS_DELETE_YES => '已删除',
			self::IS_DELETE_NO  => '',
		];
		return kv($desc, $key);
	}
}