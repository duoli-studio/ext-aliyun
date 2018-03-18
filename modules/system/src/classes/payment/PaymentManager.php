<?php namespace System\Classes\Payment;

use Finance\Action\Bond;
use Finance\Action\Charge;
use Order\Action\Order;
use Poppy\Framework\Exceptions\ApplicationException;
use System\Classes\Payment\Contracts\Payment;
use System\Classes\Traits\SystemTrait;

class PaymentManager
{
	use SystemTrait;

	private static $match = [
		'charge' => Charge::class,
		'order'  => Order::class,
		'bond'   => Bond::class,
	];

	/**
	 * @param $type
	 * @return Payment
	 * @throws ApplicationException
	 */
	public static function make($type)
	{
		if (!isset(self::$match[$type])) {
			throw new ApplicationException('给定的类型不正确');
		}
		$class = self::$match[$type];
		if (!class_exists($class)) {
			throw new ApplicationException('指定的类' . $class . '不存在');
		}
		$obj = new $class();
		if ($obj instanceof Payment) {
			return $obj;
		}
		 
			throw new ApplicationException('支付对象必须实现 ' . Payment::class . ' 类');
	}
}