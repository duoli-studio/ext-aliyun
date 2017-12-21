<?php namespace Order\Policies;

use App\Models\PamAccount;
use App\Models\PlatformOrder;

class PlatformOrderPolicy {

	/**
	 * 编辑
	 * @param PamAccount    $account
	 * @param PlatformOrder $order
	 * @return bool
	 */
	public function edit(PamAccount $account, PlatformOrder $order) {

		if (!in_array($order->order_status, [
			PlatformOrder::ORDER_STATUS_CREATE,
			PlatformOrder::ORDER_STATUS_PUBLISH,
		])
		) {
			return false;
		}
		return $account->capable('dsk_platform_order.edit');
	}

	/**
	 * 删除
	 * @param PamAccount    $account
	 * @param PlatformOrder $order
	 * @return bool
	 */
	public function destroy(PamAccount $account, PlatformOrder $order) {
		if (!in_array($order->order_status, [
			PlatformOrder::ORDER_STATUS_CREATE,
		])
		) {
			return false;
		}
		if ($order->accept_id) {
			return false;
		}
		if ($order->order_status == PlatformOrder::ORDER_STATUS_DELETE) {
			return false;
		}
		return $account->capable('dsk_platform_order.destroy');
	}

	/**
	 * 发布
	 * @param PamAccount    $account
	 * @param PlatformOrder $order
	 * @return bool
	 */
	public function batch_publish(PamAccount $account, PlatformOrder $order) {
		if (!in_array($order->order_status, [
			PlatformOrder::ORDER_STATUS_CREATE,
			PlatformOrder::ORDER_STATUS_PUBLISH,
		])
		) {
			return false;
		}
		if ($order->employee_publish == 1) {
			return false;
		}
		if ($order->accept_id) {
			return false;
		}
		return $account->capable('dsk_platform_order.publish');
	}

	public function publish_employee(PamAccount $account, PlatformOrder $order) {
		if (!in_array($order->order_status, [
			PlatformOrder::ORDER_STATUS_CREATE,
			PlatformOrder::ORDER_STATUS_PUBLISH,
		])
		) {
			return false;
		}
		if ($order->employee_publish == 1) {
			return false;
		}

		if ($order->accept_id) {
			return false;
		}
		return $account->capable('dsk_platform_order.publish');
	}

	/**
	 * 已经撤单订单重新发布
	 * @param PamAccount    $account
	 * @param PlatformOrder $order
	 * @return bool
	 */
	public function order_re_publish(PamAccount $account, PlatformOrder $order) {
		if (!in_array($order->order_status, [
			PlatformOrder::ORDER_STATUS_CANCEL,
			PlatformOrder::ORDER_STATUS_REFUND,
		])
		) {
			return false;
		}
		/*
		|--------------------------------------------------------------------------
		| 订单撤销中也可以对订单进行重新发布
		|--------------------------------------------------------------------------

		$cancelOk = $order->cancel_status == PlatformOrder::CANCEL_STATUS_DONE;
		$refundOk = $order->order_status == PlatformOrder::ORDER_STATUS_REFUND;
		if (!($cancelOk || $refundOk)) {
			return false;
		}

		*/
		if ($order->is_re_publish) {
			return false;
		}

		return $account->capable('dsk_platform_order.order_re_publish');
	}


	public function re_publish(PamAccount $account, PlatformOrder $order) {
		if (!in_array($order->order_status, [
			PlatformOrder::ORDER_STATUS_PUBLISH,
		])
		) {
			return false;
		}
		return $account->capable('dsk_platform_order.re_publish');
	}

	public function question(PamAccount $account, PlatformOrder $order) {
		if ($order->is_question) {
			return false;
		}
		return $account->capable('dsk_platform_order.question');
	}


	public function handle_question(PamAccount $account, PlatformOrder $order) {
		if (!$order->is_question) {
			return false;
		}
		return $account->capable('dsk_platform_order.handle_question');
	}


	/**
	 * 刷新所有平台订单信息
	 * @param PamAccount    $account
	 * @param PlatformOrder $order
	 * @return bool
	 */
	public function reload(PamAccount $account, PlatformOrder $order) {
		if (in_array($order->order_status, [
			PlatformOrder::ORDER_STATUS_OVER,
			PlatformOrder::ORDER_STATUS_CREATE,
			PlatformOrder::ORDER_STATUS_REFUND,
		])
		) {
			return false;
		}
		return $account->capable('dsk_platform_order.reload');
	}



	public function enable_urgency(PamAccount $account, PlatformOrder $order) {
		if (in_array($order->order_status ,[
			PlatformOrder::ORDER_STATUS_PUBLISH,
			PlatformOrder::ORDER_STATUS_DELETE,
		])) {
			return false;
		}
		if ($order->is_urgency) {
			return false;
		}
		return true;
	}


	public function disable_urgency(PamAccount $account, PlatformOrder $order) {
		if (in_array($order->order_status ,[
			PlatformOrder::ORDER_STATUS_PUBLISH,
			PlatformOrder::ORDER_STATUS_DELETE,
		])) {
			return false;
		}
		if (!$order->is_urgency) {
			return false;
		}
		return true;
	}


	public function show_urgency(PamAccount $account, PlatformOrder $order) {
		if (in_array($order->order_status ,[
			PlatformOrder::ORDER_STATUS_PUBLISH,
			PlatformOrder::ORDER_STATUS_DELETE,
		])) {
			return false;
		}
		if (!$order->is_urgency) {
			return false;
		}
		return true;
	}


	public function show_renew(PamAccount $account, PlatformOrder $order) {
		if (!$order->is_renew) {
			return false;
		}
		return true;
	}

	/**
	 * 退款
	 * @param PamAccount    $account
	 * @param PlatformOrder $order
	 * @return bool
	 */
	public function refund(PamAccount $account, PlatformOrder $order) {
		// 未发单可以退款, 已经发单, 全部删除然后同步结构也可以退款
		// 撤销中可以退款
		if (!in_array($order->order_status, [
			PlatformOrder::ORDER_STATUS_CREATE,
			PlatformOrder::ORDER_STATUS_PUBLISH,
		])
		) {
			return false;
		}

		return $account->capable('dsk_platform_order.refund');
	}

	/**
	 * 单个发布
	 * @param PamAccount    $account
	 * @param PlatformOrder $order
	 * @return bool
	 */
	public function assign_publish(PamAccount $account, PlatformOrder $order) {
		// 订单接手不能发
		if (!in_array($order->order_status, [
			PlatformOrder::ORDER_STATUS_CREATE,
			PlatformOrder::ORDER_STATUS_PUBLISH,
		])
		) {
			return false;
		}

		// 订单已经发布不能发
		return $account->capable('dsk_platform_order.assign_publish');
	}

	/**
	 * 员工专属订单 删除
	 * @param PamAccount    $account
	 * @param PlatformOrder $order
	 * @return bool
	 */
	public function employee_delete(PamAccount $account, PlatformOrder $order) {
		if ($order->order_status != PlatformOrder::ORDER_STATUS_PUBLISH) {
			return false;
		}
		return $account->capable('dsk_platform_employee.delete');
	}

	/**
	 * 员工专属订单 接手订单
	 * @param PamAccount    $account
	 * @param PlatformOrder $order
	 * @return bool
	 */
	public function employee_handle(PamAccount $account, PlatformOrder $order) {
		if ($order->order_status != PlatformOrder::ORDER_STATUS_PUBLISH) {
			return false;
		}
		return $account->capable('dsk_platform_employee.handle');
	}

	/**
	 * 员工专属 完成订单
	 * @param PamAccount    $account
	 * @param PlatformOrder $order
	 * @return bool
	 */
	public function employee_over(PamAccount $account, PlatformOrder $order) {
		if ($order->order_status != PlatformOrder::ORDER_STATUS_ING) {
			return false;
		}
		return $account->capable('dsk_platform_employee.over');
	}

	public function employee_progress(PamAccount $account, PlatformOrder $order) {
		if ($order->order_status != PlatformOrder::ORDER_STATUS_ING) {
			return false;
		}
		return $account->capable('dsk_platform_employee.progress');
	}

	public function employee_exception(PamAccount $account, PlatformOrder $order) {
		if (in_array($order->order_status, [
			PlatformOrder::ORDER_STATUS_EXCEPTION,
			PlatformOrder::ORDER_STATUS_EXAMINE,
			PlatformOrder::ORDER_STATUS_OVER,
		])) {
			return false;
		}
		if ($order->order_status == PlatformOrder::ORDER_STATUS_CANCEL) {
			if ($order->cancel_status == PlatformOrder::CANCEL_STATUS_DONE) {
				return false;
			}
		}
		return $account->capable('dsk_platform_employee.exception');
	}

	public function employee_cancel_exception(PamAccount $account, PlatformOrder $order) {
		if ($order->order_status != PlatformOrder::ORDER_STATUS_EXCEPTION) {
			return false;
		}
		return $account->capable('dsk_platform_employee.cancel_exception');
	}

	public function employee_handle_cancel(PamAccount $account, PlatformOrder $order) {
		if ($order->order_status != PlatformOrder::ORDER_STATUS_CANCEL) {
			return false;
		}
		if ($order->order_status == PlatformOrder::ORDER_STATUS_CANCEL) {
			if ($order->cancel_status == PlatformOrder::CANCEL_STATUS_DONE) {
				return false;
			}
		}
		return true;
	}

	public function employee_show(PamAccount $account, PlatformOrder $order){
		if(in_array($order->order_status, [
			PlatformOrder::ORDER_STATUS_OVER,
			PlatformOrder::ORDER_STATUS_PUBLISH,
		])){
			return false;
		}
		if ($order->order_status == PlatformOrder::ORDER_STATUS_CANCEL) {
			if ($order->cancel_status == PlatformOrder::CANCEL_STATUS_DONE) {
				return false;
			}
		}
		return true;
	}
	
}
