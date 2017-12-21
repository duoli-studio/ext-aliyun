<?php namespace Order\Events\Listeners;

use App\Models\PlatformMoney;
use App\Models\PlatformOrder;

class OrderChangeMoney {


	/**
	 * @param $old PlatformOrder
	 * @param $new PlatformOrder
	 */
	public function handle($old, $new) {
		$typeMap = [
			'order_get_in_price' => [
				'type'   => PlatformMoney::TYPE_GET_IN,
				'handle' => 'add',
			],
			'order_price'        => [
				'type'   => PlatformMoney::TYPE_PUBLISH,
				'handle' => 'sub',
			],
			'fee_zhuandan'       => [
				'type'   => PlatformMoney::TYPE_TRANS,
				'handle' => 'sub',
			],
			'fee_pub_bufen'      => [
				'type'   => PlatformMoney::TYPE_PUB_ADD,
				'handle' => 'add',
			],
			'fee_sd_bufen'       => [
				'type'   => PlatformMoney::TYPE_SD_ADD,
				'handle' => 'sub',
			],
			'fee_sd_huaidan'     => [
				'type'   => PlatformMoney::TYPE_SD_LOST,
				'handle' => 'add',
			],
			'fee_pub_buchang'    => [
				'type'   => PlatformMoney::TYPE_PUB_LOST,
				'handle' => 'sub',
			],
			'fee_other'          => [
				'type'   => PlatformMoney::TYPE_OTHER,
				'handle' => 'sub',
			],
		];

		$compare = PlatformOrder::compareOrderMoney($old, $new);

		if (!empty($compare)) {
			foreach ($compare as $field => $item) {
				if (!isset($item['amount'])) {
					continue;
				}
				$amount = $item['amount'];
				$note   = $item['desc'];
				$type   = $typeMap[$field]['type'];
				if ($typeMap[$field]['handle'] != 'add') {
					$amount = -$amount;
				}
				PlatformMoney::create([
					'order_id' => $old->order_id,
					'amount'   => $amount,
					'note'     => $note,
					'type'     => $type,
				]);
			}
		}
	}

}


