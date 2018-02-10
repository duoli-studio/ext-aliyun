<?php namespace System\Classes\Contracts;

interface PaymentContract
{
	public function payOk($order_no, $flow_no);

	public function getError();
}
