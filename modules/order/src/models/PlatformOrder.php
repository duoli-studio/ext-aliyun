<?php namespace App\Models;


/**
 * App\Models\PlatformOrder
 * php artisan ide-helper:models "App\Models\PlatformOrder"
 * @property integer               $order_id                                订单id
 * @property integer               $source_id                               来源id
 * @property string                $source_title                            来源标题
 * @property string                $order_title                             订单标题
 * @property string                $order_get_in_number                     订单号, 根据来源, 一般是 淘宝, 17173 的来源
 * @property string                $order_mobile                            订单联系手机
 * @property string                $order_contact                           联系人
 * @property string                $order_qq                                订单QQ
 * @property string                $order_current                           代练前 游戏信息
 * @property string                $order_content                           代练内容/要求
 * @property integer               $order_hours                             所有的时间长度
 * @property float                 $order_get_in_price                      接单价格
 * @property float                 $order_price                             订单价格, 外发价格, 冻结根据这个来
 * @property float                 $order_safe_money                        安全保证金
 * @property float                 $order_speed_money                       效率保证金
 * @property string                $order_note                              订单备注
 * @property string                $order_tags                              订单标签 ,XX,xx,x, 这种形式保存
 * @property string                $order_status                            cancel: 退单, create :待分配, ing:代练中, examine:待验收, over 完成订单, over_exception 订单完成异常, 一般是客服介入, exception: 订单异常
 * @property boolean               $order_lock                              订单锁定
 * @property string                $is_exception                            订单是否有异常
 * @property string                $is_exception_handled                    异常已经被处理
 * @property string                $is_star                                 发单者是否评价过
 * @property string                $cancel_type                             退单类型
 * @property string                $kf_status                               客服处理状态
 * @property string                $cancel_status                           退单状态
 * @property string                $cancel_note                             退单备注
 * @property string                $exception_type                          none: 没有异常, account_error: 账号密码错误
 * @property string                $exception_status                        异常状态
 * @property string                $game_title
 * @property string                $game_account                            游戏账号
 * @property string                $game_pwd                                游戏密码
 * @property string                $game_actor                              角色名
 * @property integer               $game_id                                 游戏ID
 * @property string                $game_area                               区服
 * @property integer               $server_id                               服务器ID
 * @property string                $server_big_title                        服务器大区
 * @property string                $server_title                            服务器名称
 * @property integer               $type_id                                 游戏段位ID
 * @property string                $type_title
 * @property integer               $publish_id                              发单者的ID
 * @property string                $publish_account_name                    发单员 账户名
 * @property string                $sd_uid                                  接单员用户ID
 * @property string                $sd_username                             接单员用户名
 * @property string                $sd_qq                                   接单人员qq
 * @property string                $sd_mobile                               接单人员手机号
 * @property float                 $fee_earn                                赚取费用
 * @property float                 $pub_pay                                 支付给 sd
 * @property float                 $sd_pay                                  支付给 pub
 * @property \Carbon\Carbon        $created_at                              创建时间
 * @property string                $quashed_at
 * @property string                $excepted_at                             异常提交时间
 * @property string                $exception_handled_at                    异常处理时间
 * @property string                $exception_cancelled_at                  异常取消时间
 * @property \Carbon\Carbon        $updated_at                              编辑
 * @property string                $deleted_at                              删除
 * @property string                $assigned_at                             分配时间/ 接单时间
 * @property string                $overed_at                               完成, 上传完成图
 * @property string                $ended_at                                结束时间
 * @property string                $handled_at                              客服介入时间
 * @property string                $kf_apply_by                             申请客服介入的账号
 * @property string                $cancel_applied_at                       发单者撤销时间
 * @property string                $cancel_rejected_at                      拒绝撤销时间
 * @property string                $cancel_passed_at                        发单者撤销通过时间
 * @property string                $cancel_cancelled_at                     取消撤单取消的时间
 * @property string                $kf_applied_at                           申请客服介入时间
 * @property string                $kf_agreed_at                            客服同意介入的时间
 * @property string                $kf_handled_at                           客服处理的时间
 * @property string                $kf_done_at                              客服处理完成时间
 * @property string                $accept_platform                         接单平台
 * @property integer               $accept_id                               接单ID
 * @property integer               $accept_platform_account_id              接单账号
 * @property string                $pt_accept_sync_at                       接单同步事件
 * @property-read PlatformStatus[] $platformStatus
 * @property-read PlatformStatus   $platformAccept
 * @property-read PlatformAccount  $platformAccount
 * @property-read PamAccount       $pam
 * @mixin \Eloquent
 * @property integer               $order_left_hours                        订单剩余时间
 * @property string                $create_uid                              创建人uid
 * @property string                $order_get_in_wangwang                   旺旺昵称
 * @property float                 $fee_zhuandan                            转单费用[-]
 * @property float                 $fee_pub_bufen                           号主补分加钱[+]
 * @property float                 $fee_sd_bufen                            代练补分加钱[-]
 * @property float                 $fee_sd_huaidan                          代练坏单赔偿[+]
 * @property float                 $fee_tuidan                              退单费用[-]
 * @property float                 $fee_other                               其他费用[-]
 * @property string                $publish_reason                          未发布成功的原因, 序列化
 * @property string                $order_get_in_mobile                     订单接单手机号
 * @property string                $publish_account                         发布的平台账号
 * @property string                $published_at                            保存的最后的发单时间
 * @property boolean               $is_question                             是否问题订单
 * @property string                $question_type                           问题订单类型
 * @property integer               $question_handle_account_id              问题订单处理用户
 * @property integer               $question_account_id                     问题提出人
 * @property string                $question_description                    问题描述
 * @property string                $question_thumb                          问题图片地址
 * @property string                $question_status                         问题状态
 * @property \Carbon\Carbon        $question_at                             提交问题时间
 * @property string                $order_color                             订单颜色
 * @property float                 $fee_pub_buchang                         补偿号主费用
 * @property string                $refund_at                               退款时间
 * @property string                $is_re_publish                           是否重新发布
 * @property string                $last_log                                最后的日志
 * @property int                   $tong_group                              代练通发布频道 1: 公共, 2 : 优质
 * @property int                   $employee_publish                        是否给员工发过 0:否     1:是
 * @property int                   $employee                                员工id
 * @property string                $pc_num                                  分配的电脑号码
 * @property int                   $is_send_overmsg                         是否发送订单完成短息
 * @property int                   $is_re_order                             是否是重新发布的订单
 * @property int                   $rel_order_id                            关联订单ID
 * @property int                   $is_urgency                              是否紧急订单
 * @property int                   $tgp_win                                 赢得局
 * @property int                   $tgp_num                                 打的总局
 * @property int                   $is_tgp_question                         是否tgp问题单   1:问题 0 不是
 * @property string                $tpl_updated_at                          战绩更新时间
 * @property string                $is_renew                                续单
 * @property float                 $overtime_add_money                      订单超时价钱
 * @property string                $first_published_at                      订单第一次发布时间
 * @property string                $enclosure                               附件
 * @property float                 $order_add_price                         奖金
 */
class PlatformOrder extends \Eloquent
{


	const LEFT_TIME_TYPE_HOUR   = 'hour';
	const LEFT_TIME_TYPE_MINUTE = 'minute';
	const LEFT_TIME_TYPE_SECOND = 'second';

	const IS_PUBLIC_Y = 'Y';
	const IS_PUBLIC_N = 'N';

	const CANCEL_TYPE_NONE     = 'none';       //无客服介入申请
	const CANCEL_TYPE_SD_DEAL  = 'sd_deal';    //接单者申请客服介入
	const CANCEL_TYPE_PUB_DEAL = 'pub_deal';   //发单者申请客服介入
	const CANCEL_TYPE_KF       = 'kf';         //客服介入
	const CANCEL_TYPE_DEAL     = 'deal';       //客服介入,问题解决

	const CANCEL_STATUS_DONE   = 'done';
	const CANCEL_STATUS_NONE   = 'none';
	const CANCEL_STATUS_ING    = 'ing';
	const CANCEL_STATUS_REJECT = 'reject';

	const KF_STATUS_DONE = 'done';
	const KF_STATUS_NONE = 'none';
	const KF_STATUS_WAIT = 'wait';
	const KF_STATUS_ING  = 'ing';

	const ORDER_STATUS_ING        = 'ing';              // 代练进行中
	const ORDER_STATUS_CREATE     = 'create';           // 已经创建完成, 尚未发布
	const ORDER_STATUS_PUBLISH    = 'publish';          // 等待接单
	const ORDER_STATUS_EXAMINE    = 'examine';          // 等待验收
	const ORDER_STATUS_OVER       = 'over';             // 订单完成
	const ORDER_STATUS_EXCEPTION  = 'exception';        // 订单异常
	const ORDER_STATUS_CANCEL     = 'cancel';           // 易代练撤销,退单
	const ORDER_STATUS_QUASH      = 'quash';            // 合作方撤销,退单
	const ORDER_STATUS_DELETE     = 'delete';           // 删除
	const ORDER_STATUS_REFUND     = 'refund';           // 退款
	const ORDER_STATUS_LOCK       = 'lock';             // 商家家
	const ORDER_STATUS_CANCELOVER = 'cancelover';       // 商家家

	const CANCEL_CUSTOM_DEAL_ING      = 'deal_ing';
	const CANCEL_CUSTOM_DEAL_OVER     = 'deal_over';
	const CANCEL_CUSTOM_KF_DEAL_IN    = 'kf_deal_in';
	const CANCEL_CUSTOM_KF_DEAL_OVER  = 'kf_deal_over';
	const CANCEL_CUSTOM_KF_DEAL_FORCE = 'kf_deal_force';

	// 异常类型和 order 表 中的 exception_type 类型匹配,同时这个也和 `game_log`中的 `log_type` 匹配
	const EXCEPTION_TYPE_NONE          = 'none';
	const EXCEPTION_TYPE_ACCOUNT_ERROR = 'account_error';
	const EXCEPTION_TYPE_OTHER         = 'other';

	const HANDLE_CANCEL_AGREE  = 'agree';
	const HANDLE_CANCEL_REJECT = 'reject';
	const HANDLE_CANCEL_KF     = 'kf';

	const KF_APPLY_BY_PUB  = 'pub';
	const KF_APPLY_BY_NONE = 'none';
	const KF_APPLY_BY_SD   = 'sd';

	const ORDER_LOCK_LOCK   = 1;
	const ORDER_LOCK_UNLOCK = 0;

	const QUESTION_TYPE_BAD         = '战绩打坏';
	const QUESTION_TYPE_LOW_SPEED   = '效率低下';
	const QUESTION_TYPE_OUT_TIME    = '订单超时';
	const QUESTION_TYPE_ACCOUNT_BAN = '订单封号';
	const QUESTION_TYPE_EMERGENCY   = '加急订单';

	const QUESTION_STATUS_CREATE = 'create';
	const QUESTION_STATUS_ING    = 'ing';
	const QUESTION_STATUS_DONE   = 'done';

	const TONG_GROUP_PUBLIC = 1;
	const TONG_GROUP_STAR   = 2;

	protected $table = 'platform_order';

	protected $primaryKey = 'order_id';

	protected $dates = ['question_at', 'overed_at'];

	protected $fillable = [
		'publish_id',
		'publish_account_name',
		'game_id',
		'server_id',
		'server_title',
		'server_big_title',
		'type_id',
		'type_title',
		'order_title',
		'order_price',
		'order_safe_money',
		'order_speed_money',
		'order_no',
		'order_hours',
		'order_status',
		'order_color',
		'game_account',
		'game_actor',
		'game_area',
		'game_pwd',
		'order_get_in_price',
		'order_get_in_number',
		'order_get_in_wangwang',
		'order_get_in_mobile',
		'order_current',
		'order_content',
		'order_contact',
		'order_mobile',
		'order_qq',
		'order_tags',
		'order_note',
		'order_tag_note',
		'actor_tag_note',
		'source_id',
		'source_title',
		'accept_platform',
		'order_lock',
		'deleted_at',
		'fee_zhuandan',
		'fee_pub_bufen',
		'fee_pub_buchang',
		'fee_sd_bufen',
		'fee_sd_huaidan',
		'fee_tuidan',
		'fee_other',
		'is_refund',
		'tong_group',
		'refund_at',
		'accept_id',
		'employee',
		'employee_publish',
		'pc_num',
		'is_send_overmsg',
		'question_at',
		'is_re_order',
		'is_urgency',
		'tgp_win',
		'tgp_num',
		'is_tgp_question',
		'tpl_updated_at',
		'is_renew',
		'ended_at',
		'overtime_add_money',
		'first_published_at',
		'enclosure',
		'order_add_price',
	];


	public function platformStatus()
	{
		return $this->hasMany('App\Models\PlatformStatus', 'order_id', 'order_id');
	}

	public function platformAccept()
	{
		return $this->hasOne('App\Models\PlatformStatus', 'id', 'accept_id');
	}

	public function platformAccount()
	{
		return $this->hasOne('App\Models\PlatformAccount', 'id', 'accept_platform_account_id');
	}

	public function questionHandleAccount()
	{
		return $this->hasOne('App\Models\PamAccount', 'account_id', 'question_handle_account_id');
	}

	public function questionAccount()
	{
		return $this->hasOne('App\Models\PamAccount', 'account_id', 'question_account_id');
	}

	public function pam()
	{
		return $this->belongsTo('App\Models\PamAccount', 'publish_id', 'account_id');
	}

	public function ePam()
	{
		return $this->belongsTo('App\Models\PamAccount', 'employee', 'account_id');
	}


	/**
	 * @param null $key
	 * @return array|mixed|string
	 */
	public static function kvOrderLock($key = null)
	{
		$desc = [
			self::ORDER_LOCK_LOCK   => '锁定',
			self::ORDER_LOCK_UNLOCK => '未锁定',
		];
		return kv($desc, $key);
	}


	/**
	 * 是否公共的描述
	 * @param null $key
	 * @return string
	 */
	public static function kvIsPublic($key = null)
	{
		$desc = [
			'Y' => '公共',
			'N' => '指定用户/组',
		];
		return kv($desc, $key);
	}

	/**
	 * 取消类型的描述
	 * @param null $key
	 * @return string
	 */
	public static function kvCancelType($key = null)
	{
		$desc = [
			self::CANCEL_TYPE_NONE     => '无撤单',
			self::CANCEL_TYPE_SD_DEAL  => '接单者',
			self::CANCEL_TYPE_PUB_DEAL => '发单者',
			self::CANCEL_TYPE_KF       => '客服介入',
			self::CANCEL_TYPE_DEAL     => '撤销',
		];
		return kv($desc, $key);
	}

	/**
	 * 自主退单类型
	 * @param null $key
	 * @return array|string
	 */
	public static function kvCancelCustom($key = null)
	{
		$desc = [
			self::CANCEL_CUSTOM_DEAL_ING      => '协商撤销中',
			self::CANCEL_CUSTOM_DEAL_OVER     => '协商已处理',
			self::CANCEL_CUSTOM_KF_DEAL_IN    => '客服介入中',
			self::CANCEL_CUSTOM_KF_DEAL_OVER  => '客服已处理',
			self::CANCEL_CUSTOM_KF_DEAL_FORCE => '客服强制撤销',
		];
		return kv($desc, $key);
	}

	/**
	 * 取消状态描述
	 * @param null $key
	 * @return array|mixed|string
	 */
	public static function kvCancelStatus($key = null)
	{
		$desc = [
			self::CANCEL_STATUS_DONE   => '完成',
			self::CANCEL_STATUS_NONE   => '无',
			self::CANCEL_STATUS_ING    => '进行中',
			self::CANCEL_STATUS_REJECT => '拒绝',
		];
		return kv($desc, $key);
	}

	/**
	 * 撤销类型完成
	 * @return array
	 */
	public static function cancelTypeSearchLinear()
	{
		return [
			'cancel_ing'     => '退单中',
			'cancel_done'    => '已退单',
			'cancel_kf_wait' => '客服介入中',
			'cancel_kf_ing'  => '客服已处理',
			'cancel_kf_done' => '客服强制退单',
		];
	}


	public static function kvKfStatus($key = null)
	{
		$desc = [
			self::KF_STATUS_NONE => '无须客服处理',
			self::KF_STATUS_DONE => '客服处理完成',
			self::KF_STATUS_WAIT => '等待客服处理',
			self::KF_STATUS_ING  => '客服处理中',
		];
		return kv($desc, $key);
	}


	/**
	 * 订单状态
	 * @param null $key
	 * @return string|array
	 */
	public static function kvOrderStatus($key = null)
	{
		$desc = [
			self::ORDER_STATUS_CREATE     => '订单创建',
			self::ORDER_STATUS_PUBLISH    => '待接单',
			self::ORDER_STATUS_ING        => '代练中',
			self::ORDER_STATUS_CANCEL     => '商家撤销中',
			self::ORDER_STATUS_QUASH      => '打手撤销中',
			self::ORDER_STATUS_EXAMINE    => '等待验收',
			self::ORDER_STATUS_OVER       => '订单完成',
			self::ORDER_STATUS_EXCEPTION  => '对方提交订单异常',
			self::ORDER_STATUS_REFUND     => '已退款',
			self::ORDER_STATUS_DELETE     => '已删除',
			self::ORDER_STATUS_CANCELOVER => '已撤销',
			self::ORDER_STATUS_LOCK       => '商家锁定中',
		];
		return kv($desc, $key);
	}

	/**
	 * 订单状态
	 * @param null $key
	 * @return string
	 */
	public static function kvEmployeeOrderStatus($key = null)
	{
		$desc = [
			self::ORDER_STATUS_PUBLISH   => '待接单',
			self::ORDER_STATUS_ING       => '代练中',
			self::ORDER_STATUS_EXAMINE   => '等待验收',
			self::ORDER_STATUS_OVER      => '订单完成',
			self::ORDER_STATUS_EXCEPTION => '订单异常',
			self::ORDER_STATUS_CANCEL    => '撤销中',
		];
		return kv($desc, $key);
	}


	/**
	 * 异常类型说明
	 * @param $key
	 * @return string
	 */
	public static function kvExceptionType($key = null)
	{
		$desc = [
			self::EXCEPTION_TYPE_ACCOUNT_ERROR => '账号错误',
			self::EXCEPTION_TYPE_OTHER         => '其他错误',
		];
		return kv($desc, $key);
	}

	/**
	 * 问题类型
	 * @param null $key
	 * @return array|mixed|string
	 */
	public static function kvQuestionType($key = null)
	{
		$desc = [
			self::QUESTION_TYPE_BAD         => '战绩打坏',
			self::QUESTION_TYPE_LOW_SPEED   => '效率低下',
			self::QUESTION_TYPE_OUT_TIME    => '订单超时',
			self::QUESTION_TYPE_ACCOUNT_BAN => '订单封号',
			self::QUESTION_TYPE_EMERGENCY   => '加急订单',
		];
		return kv($desc, $key);
	}

	/**
	 * @param null $key
	 * @param null $pub
	 * @return string
	 */
	public static function kvAcceptPlatform($key = null, $pub = null)
	{
		if ($pub == 1) {
			return '员工';
		}
		return PlatformAccount::kvPlatform($key) ?: '--';
	}

	/**
	 * 问题订单状态
	 * @param null $key
	 * @return array|mixed|string
	 */
	public static function kvQuestionStatus($key = null)
	{
		$desc = [
			self::QUESTION_STATUS_CREATE => '待处理',
			self::QUESTION_STATUS_ING    => '处理中',
			self::QUESTION_STATUS_DONE   => '处理完毕',
		];
		return kv($desc, $key);
	}

	/**
	 * 代练通发单平台
	 * @param null $key
	 * @return array|string
	 */
	public static function kvTongGroup($key = null)
	{
		$desc = [
			self::TONG_GROUP_PUBLIC => '公共区',
			self::TONG_GROUP_STAR   => '优质区',
		];
		return kv($desc, $key);
	}

	/**
	 * 记录发单时候的正确与否的操作
	 * @param     string    $account_id 发布时候的状态值 22_error, 22_success, 分别代表的是 22 成功,  22 失败
	 * @param               $type
	 * @param               $reason
	 * @param PlatformOrder $order
	 */
	public static function logPublishReason($account_id, $type, $reason, PlatformOrder $order)
	{
		$publishReason = unserialize($order->publish_reason);
		if ($type == 'error') {
			$publishReason[$account_id . '_error']   = $reason;
			$publishReason[$account_id . '_success'] = '';
		}
		else {
			$publishReason[$account_id . '_success'] = $reason;
			$publishReason[$account_id . '_error']   = '';
		}
		$order->publish_reason = serialize($publishReason);
		$order->save();
	}

	/**
	 * 对比订单金额
	 * @param $old
	 * @param $new
	 * @return array
	 */
	public static function compareOrderMoney($old, $new)
	{
		$fields  = [
			'order_get_in_price' => '接单价格',
			'order_price'        => '发单价格',
			'fee_zhuandan'       => '转单价格',
			'fee_pub_bufen'      => '号主补分加款',
			'fee_sd_bufen'       => '代练补分补偿',
			'fee_sd_huaidan'     => '代练坏单赔偿',
			'fee_pub_buchang'    => '补偿号主费用',
			'fee_other'          => '其他费用',
			'order_hours'        => '订单耗时',
		];
		$changes = [];
		foreach ($fields as $field => $desc) {
			if (in_array($field, [
				'order_get_in_price',
				'order_price',
				'fee_zhuandan',
				'fee_pub_bufen',
				'fee_sd_bufen',
				'fee_sd_huaidan',
				'fee_pub_buchang',
				'fee_other',
			])) {
				if (bccomp($old->$field, $new->$field) != 0) {
					$amount          = bcsub($new->$field, $old->$field, 2);
					$changes[$field] = [
						'amount' => $amount,
						'desc'   => $desc . '变动:' . $amount,
					];
				}
			}
			else {
				if ($old->$field != $new->$field) {
					$changes[$field] = [
						'desc' => $desc . '变动:[' . $old->$field . ' ->' . $new->$field . ']',
					];
				}
			}
		}
		return $changes;
	}

	/**
	 * 计算赚得金钱
	 * @param $order
	 * @return mixed
	 */
	public static function calcFeeEarn($order)
	{
		return
			+$order->order_get_in_price
			- $order->order_price
			- $order->fee_zhuandan
			+ $order->fee_pub_bufen
			- $order->fee_sd_bufen
			+ $order->fee_sd_huaidan
			- $order->fee_pub_buchang
			- $order->fee_other;
	}


}
