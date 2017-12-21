<?php namespace Order\Application\Platform\Factory;

use App\Lemon\Dailian\Application\Platform\Baozi;
use App\Lemon\Dailian\Application\Platform\Mama;
use App\Lemon\Dailian\Application\Platform\Mao;
use App\Lemon\Dailian\Application\Platform\Tong;
use App\Lemon\Dailian\Application\Platform\Yi;
use App\Lemon\Dailian\Application\Platform\Yq;
use App\Lemon\Dailian\Contracts\Platform;
use App\Models\PamAccount;
use App\Models\PlatformAccount;
use App\Models\PlatformOrder;
use App\Models\PlatformStatus;

class PlatformFactory {

	/**
	 * @param PlatformStatus $status
	 * @param PlatformOrder  $order
	 * @param PamAccount     $pam
	 * @return Platform
	 */
	public static function create($status, $order = null, $pam = null) {
		if (!$order) {
			$order = PlatformOrder::find($status->order_id);
		}

		if ($status->platform == PlatformAccount::PLATFORM_TONG) {
			return new Tong($status->pt_account_id, $order, $pam);
		}
		if ($status->platform == PlatformAccount::PLATFORM_BAOZI) {
			return new Baozi($status->pt_account_id, $order, $pam);
		}
		if ($status->platform == PlatformAccount::PLATFORM_YQ) {
			return new Yq($status->pt_account_id, $order, $pam);
		}
		if ($status->platform == PlatformAccount::PLATFORM_YI) {
			return new Yi($status->pt_account_id, $order, $pam);
		}
		if ($status->platform == PlatformAccount::PLATFORM_MAO) {
			return new Mao($status->pt_account_id, $order, $pam);
		}
		if ($status->platform == PlatformAccount::PLATFORM_YQ) {
			return new Yq($status->pt_account_id, $order, $pam);
		}
		if ($status->platform == PlatformAccount::PLATFORM_MAMA) {
			return new Mama($status->pt_account_id, $order, $pam);
		}
		return null;
	}
}