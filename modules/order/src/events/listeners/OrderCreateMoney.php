<?php namespace Order\Events\Listeners;

use App\Models\PlatformMoney;
use App\Models\PlatformOrder;

class OrderCreateMoney {


	/**
	 * @param $order PlatformOrder
	 */
	public function handle($order) {
		// 代练金增加
		PlatformMoney::create([
			'order_id' => $order->order_id,
			'amount'   => $order->order_get_in_price,
			'type'     => PlatformMoney::TYPE_GET_IN,
			'note'     => '订单创建, 资金增加',
		]);
	}

}


