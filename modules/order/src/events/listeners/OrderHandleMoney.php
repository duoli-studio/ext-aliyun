<?php namespace Order\Events\Listeners;

use App\Models\PlatformMoney;
use App\Models\PlatformOrder;

class OrderHandleMoney
{


	/**
	 * @param $order PlatformOrder
	 */
	public function handle($order)
	{
		// 发单减少
		PlatformMoney::create([
			'order_id' => $order->order_id,
			'amount'   => -$order->order_price,
			'type'     => PlatformMoney::TYPE_GET_IN,
			'note'     => '订单被接手, 支出代练金',
		]);
	}

}
