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
	 * @param $pushType   [推送类型]
	 * @param $account_id [给谁推送]
	 * @param $msgtype    [推送类型 0：给单人 1：给单个群]
	 * @param $msg        [要推送的内容]
	 * @return bool
	 */
	public function systemNotice($pushType, $account_id, $msgtype, $msg)
	{
		$arr = [
			'push_type'  => $pushType,
			'account_id' => $account_id,
			'msgtype'    => $msgtype,
			'msg'        => $msg,
		];

		$validator = \Validator::make($arr, [
			'push_type'  => [
				Rule::in('order', 'activity', 'system'),
			],
			'account_id' => [
				Rule::required(),
				Rule::integer(),
			],
			'msgtype'    => [
				Rule::nullable(),
				Rule::integer(),
				Rule::in(0, 1),
			],
			'msg'        => [
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

		$profile = UserProfile::find($account_id);
		switch ($pushType) {
			case 'order':
				$from = 'ddboss';
				break;
			case 'system':
				$from = 'liexiang';
				break;
			case 'activity':
				$from = 'idailian';
				break;
		}
		$data = [
			'from'    => $from,
			'msgtype' => $msgtype,
			'to'      => $profile->accid,
			'attach'  => '{"msg":" ' . $msg . ' "}',
		];

		if ($result = $this->yun->sendAttachMsg($data)) {
			if ($result['code'] == 200) {
				return true;
			}
			else {
				return $this->setError($result['desc']);
			}
		}
		else {
			return $this->setError('有错误');
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
			return $this->setError('有错误');
		}
	}
}