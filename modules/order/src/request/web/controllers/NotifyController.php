<?php namespace Order\Request\Web\Controllers;

use App\Models\PlatformStatus;

class NotifyController extends InitController
{

	public function mama()
	{
		$order_no = \Input::get('orderid');
		if (!$order_no) {
			die ('error');
		}
		$order_id = PlatformStatus::where('mama_order_no', $order_no)->value('order_id');
		if (!$order_id) {
			echo 'error';
		}
		else {
			try {
				pt_sync_order($order_id);
			} catch (\Exception $e) {
				\Log::error($e->getMessage());
				\Log::error(get_class($e));
				\Log::error('Mama:ORDER_ID:' . $order_id);
			}
			echo 'success';
		}
	}

	public function yi()
	{
		$order_no = \Input::get('order_no');
		if (!$order_no) {
			die('error');
		}
		//获取订单信息
		$order_id = PlatformStatus::where('yi_order_no', $order_no)->value('order_id');
		if (!$order_id) {
			echo 'error';
		}
		else {
			try {
				pt_sync_order($order_id);
			} catch (\Exception $e) {
				\Log::error($e->getMessage());
				\Log::error(get_class($e));
				\Log::error('YI:ORDER_ID:' . $order_id);
			}
			echo 'success';
		}
	}

	public function mao()
	{
		$order_no = \Input::get('orderid');
		if (!$order_no) {
			die ('error');
		}
		//获取订单信息
		$order_id = PlatformStatus::where('mao_order_no', $order_no)->value('order_id');
		if (!$order_id) {
			echo 'error';
		}
		else {
			try {
				pt_sync_order($order_id);
			} catch (\Exception $e) {
				\Log::error($e->getMessage());
				\Log::error(get_class($e));
				\Log::error('MAO:ORDER_ID:' . $order_id);
			}
			echo 'success';
		}
	}

	public function tong()
	{
		$order_no = \Input::get('SerialNo');

		if (!$order_no) {
			die('error');
		}
		//获取订单信息
		$order_id = PlatformStatus::where('tong_order_no', $order_no)->value('order_id');
		if (!$order_id) {
			echo 'error';
		}
		else {
			try {
				pt_sync_order($order_id);
			} catch (\Exception $e) {
				\Log::error($e->getMessage());
				\Log::error(get_class($e));
				\Log::error('TONG:ORDER_ID:' . $order_id);
			}
			echo 'success';
		}
	}
}
