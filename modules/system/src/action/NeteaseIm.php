<?php namespace System\Action;

use Poppy\Extension\NetEase\Im\Yunxin;
use System\Classes\Traits\SystemTrait;
use User\Models\UserProfile;
use Poppy\Framework\Validation\Rule;

class NeteaseIm
{
	use SystemTrait;

	/** @var Yunxin $yun */
	protected $yun;

	public function __construct()
	{
		$this->yun = new Yunxin();
	}

	/**
	 * 后台推送系统通知
	 * @param string $pushType 推送类型
	 * @param string $passport 给谁推送
	 * @param string $msg_type 推送类型 0：给单人 1：给单个群
	 * @param string $content  要推送的内容
	 * @return bool
	 */
	public function systemNotice($pushType, $passport, $msg_type, $content)
	{
		$arr = [
			'push_type' => $pushType,
			'passport'  => $passport,
			'msgtype'   => $msg_type,
			'msg'       => $content,
		];

		$validator = \Validator::make($arr, [
			'push_type' => [
				Rule::in('order', 'activity', 'system'),
			],
			'passport'  => [
				Rule::required(),
			],
			'msgtype'   => [
				Rule::nullable(),
				Rule::integer(),
				Rule::in(0, 1),
			],
			'msg'       => [
				Rule::string(),
			],
		], [], [
			'push_type'  => trans('system::action.im.push_type'),
			'account_id' => trans('system::action.im.account_id'),
			'msgtype'    => trans('system::action.im.msgtype'),
			'msg'        => trans('system::action.im.msg'),
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		switch ($pushType) {
			case 'order':
				$from = $this->getSetting()->get('system::im_notice.order_account');
				break;
			case 'system':
				$from = $this->getSetting()->get('system::im_notice.system_account');
				break;
			case 'notice':
			default:
				$from = $this->getSetting()->get('system::im_notice.notice_account');
				break;
		}
		$data = [
			'from'    => $from,
			'msgtype' => $msg_type,
			'to'      => '',
			'attach'  => '{"msg":" ' . $content . ' "}',
		];
		if ($msg_type == 0) {
			$profile = UserProfile::getByPassport($passport);
			if (!$profile) {
				return $this->setError('用户不存在');
			}
			$data['to'] = $profile->accid;
		}

		if ($result = $this->yun->sendAttachMsg($data)) {
			if ($result['code'] == 200) {
				return true;
			}
			else {
				return $this->setError($result['desc']);
			}
		}
		else {
			return $this->setError(trans('system::action.im.start_push_error'));
		}
	}

	/**
	 * 发送普通消息
	 * @param $account_id
	 * @param $msg
	 * @return bool
	 */
	public function sendMsg($account_id, $msg)
	{
		$profile = UserProfile::find($account_id);
		$data    = [
			'from' => '',
			'ope'  => 0,
			'to'   => $profile->accid,
			'type' => 0,
			'body' => '{"msg":" ' . $msg . ' "}',
		];

		if ($result = $this->yun->sendMsg($data)) {
			if ($result['code'] == 200) {
				return true;
			}
			else {
				return $this->setError($result['desc']);
			}
		}
		else {
			return $this->setError(trans('system::action.im.start_push_error'));
		}
	}

	public function setSystemInfo($input)
	{
		$validator = \Validator::make($input, [
			'system'      => [
				Rule::string(),
				Rule::required(),
			],
			'system_icon' => [
				Rule::url(),
				Rule::required(),
			],
			'notice'      => [
				Rule::string(),
				Rule::required(),
			],
			'notice_icon' => [
				Rule::url(),
				Rule::required(),
			],
			'order'       => [
				Rule::string(),
				Rule::required(),
			],
			'order_icon'  => [
				Rule::url(),
				Rule::required(),
			],
		], [], [
			'system'      => '数据格式不对！',
			'system_icon' => '数据格式不对！',
			'notice'      => '数据格式不对！',
			'notice_icon' => '数据格式不对！',
			'order'       => '数据格式不对！',
			'order_icon'  => '数据格式不对！',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		$this->getSetting()->set('system::im_notice.system_account', data_get($input, 'system'));
		$this->getSetting()->set('system::im_notice.system_image', data_get($input, 'system_icon'));
		$this->getSetting()->set('system::im_notice.notice_account', data_get($input, 'notice'));
		$this->getSetting()->set('system::im_notice.notice_image', data_get($input, 'notice_icon'));
		$this->getSetting()->set('system::im_notice.order_account', data_get($input, 'order'));
		$this->getSetting()->set('system::im_notice.order_image', data_get($input, 'order_icon'));

		$arr = [
			[
				'accid' => $this->getSetting()->get('system::im_notice.system_account'),
				'icon'  => $this->getSetting()->get('system::im_notice.system_image'),
			],
			[
				'accid' => $this->getSetting()->get('system::im_notice.notice_account'),
				'icon'  => $this->getSetting()->get('system::im_notice.notice_image'),
			],
			[
				'accid' => $this->getSetting()->get('system::im_notice.order_account'),
				'icon'  => $this->getSetting()->get('system::im_notice.order_image'),
			],
		];
		foreach ($arr as $data) {
			$result[] = $this->yun->updateUserInfo($data);
		}
		$num = 0;
		foreach ($result as $r) {
			if ($r['code'] == 200) {
				$num += 1;
			}
			else {
				$a[] = $r['desc'];
			}
		}
		$b = '';
		if ($num != 3) {
			foreach ($a as $aa) {
				$b .= $aa . '  ';
			}
			$this->setError($b);
			return false;
		}
		return true;
	}
}