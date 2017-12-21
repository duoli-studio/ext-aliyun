<?php namespace Order\Traits;

use App\Models\PlatformLog;
use App\Models\PlatformOrder;
use App\Models\PlatformStatus;

trait PlatformTraits
{

	/**
	 * @param $status_id
	 * @return PlatformStatus
	 */
	private function platformStatus($status_id)
	{
		return PlatformStatus::with('platformAccount')->with('platformOrder')->findOrFail($status_id);
	}

	/**
	 * @param PlatformOrder $old_order
	 * @param PlatformOrder $order
	 * @return bool
	 */
	private function sendMessage($old_order, $order)
	{
		$mobile = $order->order_get_in_mobile;
		$type   = '';
		if ($order->order_status == PlatformOrder::ORDER_STATUS_OVER) {
			if ($old_order->order_status == PlatformOrder::ORDER_STATUS_ING) {
				$type = 'over';
			}
		}
		if ($order->order_status == PlatformOrder::ORDER_STATUS_ING) {
			if ($old_order->order_status == PlatformOrder::ORDER_STATUS_PUBLISH) {
				$type = 'handle';
			}
		}

		if (!in_array($type, ['over', 'handle'])) {
			return false;
		}

		if (!$mobile) {
			\Log::debug('Order:' . $order->order_id . ' 号主手机号缺失, 发送失败!');
		}

		$api_mark = config('l5-sms.api_type');
		if (config('l5-sms.api_type') == 'log') {
			$api_mark = 'log';
		}
		elseif ($order->source_id) {
			if ($order->source_id == 1) {
				// 天猫
				$api_mark = 'ihuyi';
			}
			if ($order->source_id == 2) {
				// 淘宝
				$api_mark = 'ihuyi2';
			}
		}
		switch ($type) {
			case 'over':
				$content    = site('sms_order_over');
				$send_res   = send_sms($api_mark, $mobile, $content);
				$order_type = PlatformLog::ORDER_STATUS_OVER;
				break;
			default:
			case 'handle':
				$content    = site('sms_order_handle');
				$send_res   = send_sms($api_mark, $mobile, $content);
				$order_type = PlatformLog::ORDER_STATUS_ING;
				break;
		}

		if ($send_res) {
			// send ok
			$order->is_send_overmsg = 1;
			$order->last_log        = $content;
			$order->save();

			// save log
			PlatformLog::create([
				'log_content' => $content,
				'order_id'    => $order->order_id,
				'log_by'      => 'platform',
				'type'        => $order_type,
			]);

		}
		else {
			// send fail
			\Log::debug('order_id:' . $order->order_id . '; type:' . $type . '; msg : 短信未发送成功;');
		}
		return true;
	}
}