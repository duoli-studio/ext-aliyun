<?php namespace System\Request\ApiV1\Util;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Poppy\Framework\Application\ApiController;
use Poppy\Framework\Classes\Resp;
use System\Classes\Traits\SystemTrait;

/**
 * 系统信息控制
 */
class SystemController extends ApiController
{
	use SystemTrait, ThrottlesLogins;

	/**
	 * @api                    {post} api_v1/util/system/info [O]系统信息
	 * @apiVersion             1.0.0
	 * @apiName                SystemInfo
	 * @apiGroup               Util
	 * @apiSuccess {Object}    pay                         支付配置
	 * @apiSuccess {Boolean}   pay.alipay_mobile           支付宝支付是否开启
	 * @apiSuccess {Boolean}   pay.wxpay_mobile            微信支付是否开启
	 * @apiSuccess {Object}    login                       第三方登录
	 * @apiSuccess {Boolean}   login.wechat_mobile         微信登录是否开启
	 * @apiSuccess {Boolean}   login.qq_mobile             qq登录是否开启
	 * @apiSuccess {Object}    notice                      通知配置
	 * @apiSuccess {String}    notice.system_account       系统通知账号
	 * @apiSuccess {String}    notice.notice_account       官方公告账号
	 * @apiSuccess {String}    notice.order_account        订单通知账号
	 * @apiSuccessExample      data:
	 * {
	 *     "status": 0,
	 *     "message": "[开发]获取系统配置信息",
	 *     "data": {
	 *         "pay": {
	 *             "alipay_mobile": true,
	 *             "wxpay_mobile": true
	 *         },
	 *         "login": {
	 *             "wechat_mobile": true,
	 *             "qq_mobile": true
	 *         },
	 *         "notice": {
	 *           "system_account": "ddboss",
	 *           "notice_account": "liexiang",
	 *           "order_account": "idailian"
	 *         }
	 *     }
	 * }
	 */
	public function info()
	{
		$system['pay']      = [
			'alipay_mobile' => $this->getSetting()->get('extension::pay_alipay.mobile_is_open'),
			'wxpay_mobile'  => $this->getSetting()->get('extension::pay_wxpay.mobile_is_open'),
		];
		$system['login']    = [
			'wechat_mobile' => $this->getSetting()->get('extension::login_wechat.mobile_is_open'),
			'qq_mobile'     => $this->getSetting()->get('extension::login_qq.mobile_is_open'),
		];
		$system['notice']   = [
			'system_account' => $this->getSetting()->get('system::im_notice.system_account'),
			'notice_account' => $this->getSetting()->get('system::im_notice.notice_account'),
			'order_account'  => $this->getSetting()->get('system::im_notice.order_account'),
		];

		return Resp::web(Resp::SUCCESS, '获取系统配置信息', $system);
	}
}
