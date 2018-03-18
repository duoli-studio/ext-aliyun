<?php namespace System\Classes\Payment\Contracts;

interface Payment
{
	public function payOk($order_no, $flow_no);

	public function fetch($order_no);

	public function getError();
}