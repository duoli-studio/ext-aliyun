<?php namespace Order\Policies;

use App\Lemon\Dailian\Application\Platform\Mama;
use App\Lemon\Dailian\Application\Platform\Mao;
use App\Lemon\Dailian\Application\Platform\Tong;
use App\Lemon\Dailian\Application\Platform\Yi;
use App\Lemon\Dailian\Application\Platform\Yq;
use App\Lemon\Dailian\Application\Platform\Baozi;
use App\Models\PamAccount;
use App\Models\PlatformAccount;
use App\Models\PlatformOrder;
use App\Models\PlatformStatus;

class PlatformStatusPolicy
{


	/**
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function yi_pub_cancel_apply(PamAccount $account, PlatformStatus $platform_status)
	{

		if (!in_array($platform_status->yi_order_status, [
			Yi::ORDER_STATUS_EXAMINE,
			Yi::ORDER_STATUS_EXCEPTION,
			Yi::ORDER_STATUS_ING,
		])
		) {
			return false;
		}
		if ($platform_status->yi_order_status == Yi::ORDER_STATUS_CANCEL && in_array($platform_status->yi_cancel_type, [
				Yi::CANCEL_TYPE_PUB_DEAL,
			])
		) {
			return false;
		}
		return $account->capable('dsk_platform_status_yi.cancel');
	}


	/**
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function yi_pub_cancel_cancel(PamAccount $account, PlatformStatus $platform_status)
	{
		if (!in_array($platform_status->yi_order_status, [
			Yi::ORDER_STATUS_CANCEL,
		])
		) {
			return false;
		}
		if (!in_array($platform_status->yi_cancel_type, [
			Yi::CANCEL_TYPE_PUB_DEAL,
		])
		) {
			return false;
		}
		if (!in_array($platform_status->yi_cancel_status, [
			Yi::CANCEL_STATUS_ING,
		])
		) {
			return false;
		}
		return $account->capable('dsk_platform_status_yi.cancel');
	}


	/**
	 * 发单者提出
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function yi_sd_cancel_handle(PamAccount $account, PlatformStatus $platform_status)
	{
		// 撤销进行中
		if ($platform_status->yi_cancel_status != Yi::CANCEL_STATUS_ING) {
			return false;
		}
		// 是否发单者提出的
		if ($platform_status->yi_cancel_type != Yi::CANCEL_TYPE_SD_DEAL) {
			return false;
		}
		return $account->capable('dsk_platform_status_yi.cancel');
	}


	/**
	 * 申请客服介入
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function yi_kf(PamAccount $account, PlatformStatus $platform_status)
	{
		// 撤销进行中
		if (!in_array($platform_status->yi_cancel_status, [Yi::CANCEL_STATUS_ING, Yi::CANCEL_STATUS_REJECT,])) {
			return false;
		}
		// 是否客服已经介入
		if ($platform_status->yi_kf_status != Yi::KF_STATUS_NONE) {
			return false;
		}

		if (!in_array($platform_status->yi_order_status, [
			Yi::ORDER_STATUS_ING,
			Yi::ORDER_STATUS_CANCEL,
		])
		) {
			return false;
		}
		if (!in_array($platform_status->yi_cancel_type, [
			Yi::CANCEL_TYPE_PUB_DEAL,
			Yi::CANCEL_TYPE_SD_DEAL,
		])
		) {
			return false;
		}

		// 申请之后 5小时才可介入
		return $account->capable('dsk_platform_status_yi.cancel');
	}

	/**
	 * todo 电竞包子
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function baozi_pub_cancel_apply(PamAccount $account, PlatformStatus $platform_status)
	{

		if (!in_array($platform_status->baozi_order_status, [
			Baozi::ORDER_STATUS_EXAMINE,
			Baozi::ORDER_STATUS_EXCEPTION,
			Baozi::ORDER_STATUS_ING,
		])
		) {
			return false;
		}
		if ($platform_status->baozi_order_status == Baozi::ORDER_STATUS_CANCEL && in_array($platform_status->baozi_cancel_type, [
				Baozi::CANCEL_TYPE_PUB_DEAL,
			])
		) {
			return false;
		}
		// return $account->capable('dsk_platform_status_baozi.cancel');//测试
		return true;
	}


	/**
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function baozi_pub_cancel_cancel(PamAccount $account, PlatformStatus $platform_status)
	{
		if (!in_array($platform_status->baozi_order_status, [
			Baozi::ORDER_STATUS_CANCEL,
		])
		) {
			return false;
		}
		if (!in_array($platform_status->baozi_cancel_type, [
			Baozi::CANCEL_TYPE_PUB_DEAL,
		])
		) {
			return false;
		}
		if (!in_array($platform_status->baozi_cancel_status, [
			Baozi::CANCEL_STATUS_ING,
		])
		) {
			return false;
		}
		return true;
		// return $account->capable('dsk_platform_status_baozi.cancel');
	}


	/**
	 * 发单者提出
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function baozi_sd_cancel_handle(PamAccount $account, PlatformStatus $platform_status)
	{
		// 撤销进行中
		if ($platform_status->baozi_cancel_status != Baozi::CANCEL_STATUS_ING) {
			return false;
		}
		// 是否发单者提出的
		if ($platform_status->baozi_cancel_type != Baozi::CANCEL_TYPE_SD_DEAL) {
			return false;
		}
		return true;
		// return $account->capable('dsk_platform_status_baozi.cancel');
	}


	/**
	 * 申请客服介入
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function baozi_kf(PamAccount $account, PlatformStatus $platform_status)
	{
		// 撤销进行中
		if (!in_array($platform_status->baozi_cancel_status, [Baozi::CANCEL_STATUS_ING, Baozi::CANCEL_STATUS_REJECT,])) {
			return false;
		}
		// // 是否客服已经介入
		if ($platform_status->baozi_kf_status != Baozi::KF_STATUS_NONE) {
			return false;
		}

		if (!in_array($platform_status->baozi_order_status, [
			Baozi::ORDER_STATUS_ING,
			Baozi::ORDER_STATUS_CANCEL,
		])
		) {
			return false;
		}
		if (!in_array($platform_status->baozi_cancel_type, [
			Baozi::CANCEL_TYPE_PUB_DEAL,
			Baozi::CANCEL_TYPE_SD_DEAL,
		])
		) {
			return false;
		}

		// 申请之后 5小时才可介入
		// return $account->capable('dsk_platform_status_baozi.cancel');
		return true;
	}

	/**
	 * 代练猫发布订单
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function mao_publish(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if ($platform_status->mao_is_publish && $platform_status->mao_is_delete == 0) {
			return false;
		}
		return $account->capable('dsk_platform_status_mao.publish');
	}

	/**
	 * 代练猫申请撤单
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function mao_pub_cancel_apply(PamAccount $account, PlatformStatus $platform_status)
	{
		if (!in_array($platform_status->mao_order_status, [
			Mao::ORDER_STATUS_EXCEPTION,
			Mao::ORDER_STATUS_ING,
			Mao::ORDER_STATUS_LOCK,
		])
		) {
			return false;
		}
		return true;
	}

	/**
	 * 代练媽媽申请撤单
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function mama_pub_cancel_apply(PamAccount $account, PlatformStatus $platform_status)
	{
		if (!in_array($platform_status->mama_order_status, [
			Mama::ORDER_STATUS_EXCEPTION,
			Mama::ORDER_STATUS_ING,
			Mama::ORDER_STATUS_LOCK,
		])
		) {
			return false;
		}
		return true;
	}

	/**
	 * 代练猫发单取消撤销
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function mao_pub_cancel_cancel(PamAccount $account, PlatformStatus $platform_status)
	{

		if (!in_array($platform_status->mao_order_status, [
			Mao::ORDER_STATUS_CANCELING,
		])
		) {
			return false;
		}

		// if (!in_array($platform_status->mao_cancel_status, [
		// 	Mao::CANCEL_STATUS_TREATY,
		// ])
		// ) {
		// 	return false;
		// }
		return true;
	}

	/**
	 * 代练妈妈发单取消撤销
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function mama_pub_cancel_cancel(PamAccount $account, PlatformStatus $platform_status)
	{

		if (!in_array($platform_status->mama_order_status, [
			Mama::ORDER_STATUS_CANCELING,
		])
		) {
			return false;
		}

		// if (!in_array($platform_status->mao_cancel_status, [
		// 	Mao::CANCEL_STATUS_TREATY,
		// ])
		// ) {
		// 	return false;
		// }
		return true;
	}

	/**
	 * 发单者提出撤销的同意处理
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function mao_sd_cancel_handle(PamAccount $account, PlatformStatus $platform_status)
	{
		// 撤销进行中
		if ($platform_status->mao_order_status != Mao::ORDER_STATUS_CANCELING) {
			return false;
		}
		if ($platform_status->mao_cancel_status == Mao::PUB_CANCEL_APPLY) {
			return false;
		}
		return true;
	}

	/**
	 * 发单者提出撤销的同意处理
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function mama_sd_cancel_handle(PamAccount $account, PlatformStatus $platform_status)
	{
		// 撤销进行中
		if ($platform_status->mama_order_status != Mama::ORDER_STATUS_CANCELING) {
			return false;
		}
		if ($platform_status->mama_cancel_status == Mama::PUB_CANCEL_APPLY) {
			return false;
		}
		return true;
	}

	/**
	 * 代练通发布订单
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function tong_publish(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if ($platform_status->tong_is_publish && $platform_status->tong_is_delete == 0) {
			return false;
		}
		return $account->capable('dsk_platform_status_tong.publish');
	}

	public function tong_lock(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if (!$platform_status->tong_is_publish) {
			return false;
		}
		if ($platform_status->tong_is_delete) {
			return false;
		}
		if (!$platform_status->tong_is_accept) {
			return false;
		}
		if ($platform_status->tong_is_over) {
			return false;
		}
		if ($platform_status->tong_order_status == Tong::ORDER_STATUS_CANCEL) {
			if (!in_array($platform_status->tong_cancel_status, [
				Tong::CANCEL_STATUS_NONE,
				Tong::CANCEL_STATUS_TREATY,
			])
			) {
				return false;
			};
		}
		return $account->capable('dsk_platform_status_tong.lock');
	}

	/**
	 * 申请客服介入
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function mao_kf(PamAccount $account, PlatformStatus $platform_status)
	{

		if (!in_array($platform_status->mao_order_status, [
			Mao::ORDER_STATUS_CANCELING,
		])
		) {
			return false;
		}
		// if (!in_array($platform_status->mao_cancel_status, [
		// 	Mao::CANCEL_STATUS_KF_IN,
		// 	Mao::CANCEL_STATUS_KF_OVER,
		// 	Mao::CANCEL_STATUS_FORCE_HANDLE,
		// 	Mao::CANCEL_STATUS_TREATY,
		// ])
		// ) {
		// 	return false;
		// }
		// 申请之后 5小时才可介入
		return true;
	}

	/**
	 * 代练妈妈申请客服介入
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function mama_kf(PamAccount $account, PlatformStatus $platform_status)
	{

		if (!in_array($platform_status->mama_order_status, [
			Mama::ORDER_STATUS_CANCELING,
		])
		) {
			return false;
		}
		// if (!in_array($platform_status->mao_cancel_status, [
		// 	Mao::CANCEL_STATUS_KF_IN,
		// 	Mao::CANCEL_STATUS_KF_OVER,
		// 	Mao::CANCEL_STATUS_FORCE_HANDLE,
		// 	Mao::CANCEL_STATUS_TREATY,
		// ])
		// ) {
		// 	return false;
		// }
		// 申请之后 5小时才可介入
		return true;
	}

	public function mao_lock(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if (!$platform_status->mao_is_publish) {
			return false;
		}
		if ($platform_status->mao_is_delete) {
			return false;
		}
		if (!$platform_status->mao_is_accept) {
			return false;
		}
		if ($platform_status->mao_is_over) {
			return false;
		}
		if ($platform_status->mao_order_status == Mao::ORDER_STATUS_CANCELING) {
			return false;
		}
		return $account->capable('dsk_platform_status_mao.lock');
	}

	public function mama_lock(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if (!$platform_status->mama_is_publish) {
			return false;
		}
		if ($platform_status->mama_is_delete) {
			return false;
		}
		if (!$platform_status->mama_is_accept) {
			return false;
		}
		if ($platform_status->mama_is_over) {
			return false;
		}
		if ($platform_status->mama_order_status == Mama::ORDER_STATUS_CANCELING) {
			return false;
		}
		if ($platform_status->mama_order_status == Mama::ORDER_STATUS_OVER_CANCEL) {
			return false;
		}
		return $account->capable('dsk_platform_status_mao.lock');
	}

	/**
	 * 进行中的操作, 非撤销的操作/补款、补时、修改密码
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function mao_ing(PamAccount $account, PlatformStatus $platform_status)
	{
		if ($platform_status->mao_order_status == Mao::ORDER_STATUS_ING || $platform_status->mao_order_status == Mao::ORDER_STATUS_EXCEPTION) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * 进行中的操作, 非撤销的操作/补款、补时、修改密码
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function mama_ing(PamAccount $account, PlatformStatus $platform_status)
	{
		if ($platform_status->mama_order_status == Mao::ORDER_STATUS_ING || $platform_status->mama_order_status == Mama::ORDER_STATUS_EXCEPTION) {
			return true;
		}
		else {
			return false;
		}
	}

	public function mao_over(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if ($platform_status->mao_is_delete) {
			return false;
		}
		if (!$platform_status->mao_is_publish) {
			return false;
		}
		if ($platform_status->mao_order_status != Mao::ORDER_STATUS_WAIT_VERIFY) {
			return false;
		}
		return $account->capable('dsk_platform_status_mao.over');
	}

	public function mama_over(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if ($platform_status->mama_is_delete) {
			return false;
		}
		if (!$platform_status->mama_is_publish) {
			return false;
		}
		if ($platform_status->mama_order_status != Mama::ORDER_STATUS_WAIT_VERIFY) {
			return false;
		}
		return $account->capable('dsk_platform_status_mao.over');
	}

	/**
	 * 代练猫发布订单
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function mao_delete(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if (!($platform_status->mao_is_publish && !$platform_status->mao_is_delete)) {
			return false;
		}
		if ($platform_status->mao_is_accept) {
			return false;
		}
		return $account->capable('dsk_platform_status_mao.delete');
	}

	/**
	 * 代练媽媽发布订单
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function mama_delete(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if (!($platform_status->mama_is_publish && !$platform_status->mama_is_delete)) {
			return false;
		}
		if ($platform_status->mama_is_accept) {
			return false;
		}
		return $account->capable('dsk_platform_status_mama.delete');
	}

	/**
	 * 更新进度
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function mao_progress(PamAccount $account, PlatformStatus $platform_status)
	{
		if (!$platform_status->mao_is_publish) {
			return false;
		}
		if (in_array($platform_status->mao_order_status, [
			Mao::ORDER_STATUS_OVER_CANCEL,
			Mao::ORDER_STATUS_OVER_PAY,
		])) {
			return false;
		}
		return true;
	}

	/**
	 * 更新进度
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function mama_progress(PamAccount $account, PlatformStatus $platform_status)
	{
		if (!$platform_status->mama_is_publish) {
			return false;
		}
		if (in_array($platform_status->mama_order_status, [
			Mama::ORDER_STATUS_OVER_CANCEL,
			Mama::ORDER_STATUS_OVER_PAY,
		])) {
			return false;
		}
		return true;
	}

	public function tong_delete(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if (!($platform_status->tong_is_publish && !$platform_status->tong_is_delete)) {
			return false;
		}
		if ($platform_status->tong_is_accept) {
			return false;
		}
		return $account->capable('dsk_platform_status_tong.delete');
	}

	public function yi_delete(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if (!($platform_status->yi_is_publish && !$platform_status->yi_is_delete)) {
			return false;
		}
		if ($platform_status->yi_is_accept) {
			return false;
		}
		return $account->capable('dsk_platform_status_yi.delete');
	}

	public function yi_over(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if ($platform_status->yi_is_delete) {
			return false;
		}
		if (!$platform_status->yi_is_publish) {
			return false;
		}
		if ($platform_status->yi_order_status != Yi::ORDER_STATUS_EXAMINE) {
			return false;
		}
		return $account->capable('dsk_platform_status_yi.over');
	}

	public function yi_lock(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if (!$platform_status->yi_is_publish) {
			return false;
		}
		else {
			// 撤销完成不显示这个功能
			if ($platform_status->yi_order_status == Yi::ORDER_STATUS_CANCEL) {

				if ($platform_status->yi_cancel_status == Yi::CANCEL_STATUS_DONE) {
					return false;
				}
			}
		}

		if ($platform_status->yi_is_delete) {
			return false;
		}
		if (!$platform_status->yi_is_accept) {
			return false;
		}

		// 订单完成之后, 不允许对订单进行操作
		if ($platform_status->yi_order_status == Yi::ORDER_STATUS_OVER) {
			return false;
		}


		return $account->capable('dsk_platform_status_yi.lock');
	}


	public function yi_progress(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if (!$platform_status->yi_is_publish) {
			return false;
		}

		if ($platform_status->yi_order_status == Yi::ORDER_STATUS_OVER) {
			return false;
		}
		return true;
	}

	/**
	 * 进行中的操作, 非撤销的操作
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function yi_ing(PamAccount $account, PlatformStatus $platform_status)
	{
		// 撤销完成不显示这个功能
		if ($platform_status->yi_order_status == Yi::ORDER_STATUS_CANCEL) {
			if ($platform_status->yi_cancel_status == Yi::CANCEL_STATUS_DONE) {
				return false;
			}
		}
		if (($platform_status->yi_order_status == Yi::ORDER_STATUS_ING) || $platform_status->yi_order_status == Yi::ORDER_STATUS_EXCEPTION) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * 评论啊
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function yi_star(PamAccount $account, PlatformStatus $platform_status)
	{
		// 撤销完成不显示这个功能
		if ($platform_status->yi_order_status == Yi::ORDER_STATUS_OVER) {
			if ($platform_status->yi_is_star) {
				return false;
			}
			else {
				return true;
			}
		}
		return false;
	}

	/**
	 * 是否能够继续发布订单
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function assign_publish(PamAccount $account, PlatformStatus $platform_status)
	{
		if ($platform_status->platform == PlatformAccount::PLATFORM_YI) {
			return ($platform_status->yi_is_publish && $platform_status->yi_is_delete) || !$platform_status->yi_is_publish;
		}
		if ($platform_status->platform == PlatformAccount::PLATFORM_BAOZI) {
			return ($platform_status->baozi_is_publish && $platform_status->baozi_is_delete) || !$platform_status->baozi_is_publish;
		}
		if ($platform_status->platform == PlatformAccount::PLATFORM_MAO) {
			return ($platform_status->mao_is_publish && $platform_status->mao_is_delete) || !$platform_status->mao_is_publish;
		}
		if ($platform_status->platform == PlatformAccount::PLATFORM_TONG) {
			return ($platform_status->tong_is_publish && $platform_status->tong_is_delete) || !$platform_status->tong_is_publish;
		}
		return $account->capable('dsk_platform_order.assign_publish');
	}

	/**
	 * 电竞包子
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 *
	 */
	public function baozi_delete(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if (!($platform_status->baozi_is_publish && !$platform_status->baozi_is_delete)) {
			return false;
		}
		if ($platform_status->baozi_is_accept) {
			return false;
		}
		// return $account->capable('dsk_platform_status_baozi.delete');//测试
		return true;
	}

	public function baozi_over(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if ($platform_status->baozi_is_delete) {
			return false;
		}
		if (!$platform_status->baozi_is_publish) {
			return false;
		}
		if ($platform_status->baozi_order_status != Baozi::ORDER_STATUS_EXAMINE) {
			return false;
		}
		// return $account->capable('dsk_platform_status_baozi.over');//测试
		return true;
	}

	public function baozi_lock(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if (!$platform_status->baozi_is_publish) {
			return false;
		}
		else {
			// 撤销完成不显示这个功能
			if ($platform_status->baozi_order_status == Baozi::ORDER_STATUS_CANCEL) {

				if ($platform_status->baozi_cancel_status == Baozi::CANCEL_STATUS_DONE) {
					return false;
				}
			}
		}

		if ($platform_status->baozi_is_delete) {
			return false;
		}
		if (!$platform_status->baozi_is_accept) {
			return false;
		}

		// 订单完成之后, 不允许对订单进行操作
		if ($platform_status->baozi_order_status == Baozi::ORDER_STATUS_OVER) {
			return false;
		}


		// return $account->capable('dsk_platform_status_baozi.lock'); //测试
		return true;
	}


	public function baozi_progress(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if (!$platform_status->baozi_is_publish) {
			return false;
		}

		if ($platform_status->baozi_order_status == Baozi::ORDER_STATUS_OVER) {
			return false;
		}
		return true;
	}

	/**
	 * 进行中的操作, 非撤销的操作
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function baozi_ing(PamAccount $account, PlatformStatus $platform_status)
	{
		// 撤销完成不显示这个功能
		if ($platform_status->baozi_order_status == Baozi::ORDER_STATUS_CANCEL) {
			if ($platform_status->baozi_cancel_status == Baozi::CANCEL_STATUS_DONE) {
				return false;
			}
		}
		// 锁定不显示这个功能 测试
		if ($platform_status->baozi_is_lock) {
			return false;
		}
		if (($platform_status->baozi_order_status == Baozi::ORDER_STATUS_ING) || $platform_status->baozi_order_status == Baozi::ORDER_STATUS_EXCEPTION) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * 评论啊
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function baozi_star(PamAccount $account, PlatformStatus $platform_status)
	{
		// 撤销完成不显示这个功能
		if ($platform_status->baozi_order_status == Baozi::ORDER_STATUS_OVER) {
			if ($platform_status->baozi_is_star) {
				return false;
			}
			else {
				return true;
			}
		}
		return false;
	}


	/**
	 * 代练通申请撤单
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function tong_pub_cancel_apply(PamAccount $account, PlatformStatus $platform_status)
	{

		if (!in_array($platform_status->tong_order_status, [
			Tong::ORDER_STATUS_EXAMINE,
			Tong::ORDER_STATUS_EXCEPTION,
			Tong::ORDER_STATUS_ING,
		])
		) {
			return false;
		}
		if ($platform_status->tong_cancel_status != Tong::CANCEL_STATUS_NONE) {
			return false;
		}
		return true;
	}

	/**
	 * 代练通发单取消撤销
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function tong_pub_cancel_cancel(PamAccount $account, PlatformStatus $platform_status)
	{

		if (!in_array($platform_status->tong_order_status, [
			Tong::ORDER_STATUS_CANCEL,
		])
		) {
			return false;
		}

		if (!in_array($platform_status->tong_cancel_status, [
			Tong::CANCEL_STATUS_TREATY,
		])
		) {
			return false;
		}
		return true;
	}

	/**
	 * 发单者提出撤销的同意处理
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function tong_sd_cancel_handle(PamAccount $account, PlatformStatus $platform_status)
	{
		// 撤销进行中
		if ($platform_status->tong_order_status != Tong::ORDER_STATUS_CANCEL) {
			return false;
		}

		if ($platform_status->tong_cancel_status != Tong::CANCEL_STATUS_TREATY) {
			return false;
		}
		return true;
	}

	/**
	 * 申请客服介入
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function tong_kf(PamAccount $account, PlatformStatus $platform_status)
	{

		if (!in_array($platform_status->tong_order_status, [
			Tong::ORDER_STATUS_CANCEL,
		])
		) {
			return false;
		}
		if (!in_array($platform_status->tong_cancel_status, [
			Tong::CANCEL_STATUS_KF_IN,
			Tong::CANCEL_STATUS_KF_OVER,
			Tong::CANCEL_STATUS_FORCE_HANDLE,
			Tong::CANCEL_STATUS_TREATY,
		])
		) {
			return false;
		}
		// 申请之后 5小时才可介入
		return true;
	}


	/**
	 * 进行中的操作, 非撤销的操作
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function tong_ing(PamAccount $account, PlatformStatus $platform_status)
	{
		if ($platform_status->tong_order_status == Tong::ORDER_STATUS_CANCEL) {
			if (!in_array($platform_status->tong_cancel_status, [
				Tong::CANCEL_STATUS_TREATY,
			])
			) {
				return false;
			}
		}
		elseif ($platform_status->tong_order_status == Tong::ORDER_STATUS_ING || $platform_status->tong_order_status == Tong::ORDER_STATUS_EXCEPTION) {
			return true;
		}
		else {
			return false;
		}
	}

	public function tong_progress(PamAccount $account, PlatformStatus $platform_status)
	{
		if (!$platform_status->tong_is_publish) {
			return false;
		}

		if ($platform_status->tong_order_status == Tong::ORDER_STATUS_OVER) {
			return false;
		}
		if ($platform_status->tong_order_status == Tong::ORDER_STATUS_CANCEL) {
			if (!in_array($platform_status->tong_cancel_status, [
				Tong::CANCEL_STATUS_TREATY,
			])
			) {
				return false;
			}
		}
		return true;
	}

	public function tong_over(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if ($platform_status->tong_is_delete) {
			return false;
		}
		if (!$platform_status->tong_is_publish) {
			return false;
		}
		if ($platform_status->tong_order_status != Tong::ORDER_STATUS_EXAMINE) {
			return false;
		}
		return $account->capable('dsk_platform_status_tong.over');
	}


	public function tong_star(PamAccount $account, PlatformStatus $platform_status)
	{
		// 撤销完成不显示这个功能
		if ($platform_status->tong_order_status == Tong::ORDER_STATUS_OVER) {
			if ($platform_status->tong_is_star) {
				return false;
			}
			else {
				return true;
			}
		}
		return false;
	}

	public function mama_star(PamAccount $account, PlatformStatus $platform_status)
	{
		// 撤销完成不显示这个功能
		if ($platform_status->mama_order_status == Mama::ORDER_STATUS_OVER_PAY) {
			return true;
		}
		return false;
	}

	public function mao_star(PamAccount $account, PlatformStatus $platform_status)
	{
		// 撤销完成不显示这个功能
		if ($platform_status->mao_order_status == Mao::ORDER_STATUS_OVER_PAY) {
			return true;
		}
		return false;
	}

	public function employee_over(PamAccount $account, PlatformStatus $platform_status)
	{
		if ($platform_status->employee_order_status != PlatformOrder::ORDER_STATUS_EXAMINE) {
			return false;
		}
		return $account->capable('dsk_platform_employee.confirm_order_over');
	}

	public function employee_ing(PamAccount $account, PlatformStatus $platform_status)
	{
		// 撤销完成不显示这个功能
		if ($platform_status->employee_order_status == PlatformOrder::ORDER_STATUS_CANCEL) {
			if ($platform_status->employee_cancel_status == PlatformOrder::CANCEL_STATUS_DONE) {
				return false;
			}
		}
		if (($platform_status->employee_order_status == PlatformOrder::ORDER_STATUS_ING) || $platform_status->employee_order_status == PlatformOrder::ORDER_STATUS_EXCEPTION) {
			return true;
		}
		else {
			return false;
		}
	}

	public function employee_progress(PamAccount $account, PlatformStatus $platform_status)
	{
		if ($platform_status->employee_order_status == PlatformOrder::ORDER_STATUS_OVER) {
			return false;
		}
		if ($platform_status->employee_order_status == PlatformOrder::ORDER_STATUS_CANCEL) {
			if ($platform_status->employee_cancel_status == PlatformOrder::CANCEL_STATUS_DONE) {
				return false;
			}
		}
		return true;
	}

	public function employee_pub_cancel_apply(PamAccount $account, PlatformStatus $platform_status)
	{
		if (in_array($platform_status->employee_order_status, [
			PlatformOrder::ORDER_STATUS_OVER,
			PlatformOrder::ORDER_STATUS_PUBLISH,
			PlatformOrder::ORDER_STATUS_CANCEL,
		])) {
			return false;
		}
		return true;
	}

	public function employee_pub_cancel_cancel(PamAccount $account, PlatformStatus $platform_status)
	{
		if ($platform_status->employee_order_status != PlatformOrder::ORDER_STATUS_CANCEL) {
			return false;
		}
		if ($platform_status->employee_order_status == PlatformOrder::ORDER_STATUS_CANCEL) {
			if ($platform_status->employee_cancel_status == PlatformOrder::CANCEL_STATUS_DONE) {
				return false;
			}
		}
		return true;
	}

	/** -------------------------------------- 下面是17代练 ----------------------------------------------------------- */


	/**
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 * //@can 删除 在publish页面的那个
	 */
	public function yq_delete(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if (!($platform_status->yq_is_publish && !$platform_status->yq_is_delete)) {
			return false;
		}
		if ($platform_status->yq_is_accept) {
			return false;
		}
		// return $account->capable('dsk_platform_status_yq.delete');
		return true;
	}

	public function yq_lock(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if (!$platform_status->yq_is_publish) {
			return false;
		}
		if ($platform_status->yq_is_delete) {
			return false;
		}
		if (!$platform_status->yq_is_accept) {
			return false;
		}
		if ($platform_status->yq_is_over) {
			return false;
		}

		if ($platform_status->yq_order_status == Yq::ORDER_STATUS_CANCEL) {
			if (!in_array($platform_status->yq_cancel_status, [
				Yq::CANCEL_STATUS_NONE,
				Yq::CANCEL_STATUS_TREATY,
			])
			) {
				return false;
			};
		}
		// return $account->capable('dsk_platform_status_yq.lock');
		return true;
	}

	/**
	 * 进行中的操作, 非撤销的操作/补款、补时、修改密码
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function yq_ing(PamAccount $account, PlatformStatus $platform_status)
	{
		if ($platform_status->yq_order_status == Yq::ORDER_STATUS_ING || $platform_status->yq_order_status == Yq::ORDER_STATUS_EXCEPTION || $platform_status->yq_order_status == Yq::ORDER_STATUS_LOCKAFTERING) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * 更新进度
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function yq_progress(PamAccount $account, PlatformStatus $platform_status)
	{
		if (!$platform_status->yq_is_publish) {
			return false;
		}
		if (in_array($platform_status->yq_order_status, [
			Yq::ORDER_STATUS_OVER_CANCEL,
			Yq::ORDER_STATUS_OVER,
		])) {
			return false;
		}
		return true;
	}

	public function yq_over(PamAccount $account, PlatformStatus $platform_status)
	{
		// 已发布但是未删除
		if ($platform_status->yq_is_delete) {
			return false;
		}
		if (!$platform_status->yq_is_publish) {
			return false;
		}
		if ($platform_status->yq_order_status != Yq::ORDER_STATUS_EXAMINE) {
			return false;
		}

		// return $account->capable('dsk_platform_status_yq.over');
		return true;
	}

	/**
	 * 17代练取消撤销
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function yq_pub_cancel_cancel(PamAccount $account, PlatformStatus $platform_status)
	{

		if (!in_array($platform_status->yq_order_status, [
			Yq::ORDER_STATUS_CANCEL,
		])
		) {
			return false;
		}

		if (!in_array($platform_status->yq_cancel_status, [
			Yq::CANCEL_STATUS_TREATY,
		])
		) {
			return false;
		}
		return true;
	}

	/**
	 * 17代练申请撤单
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function yq_pub_cancel_apply(PamAccount $account, PlatformStatus $platform_status)
	{
		if (!in_array($platform_status->yq_order_status, [
			Yq::ORDER_STATUS_EXCEPTION,
			Yq::ORDER_STATUS_ING,
			Yq::ORDER_STATUS_LOCK,
			Yq::ORDER_STATUS_LOCKAFTERING,
			Yq::ORDER_STATUS_EXAMINE,
			Yq::CANCEL_STATUS_330,
		])
		) {
			return false;
		}
		return true;
	}

	/**
	 * 发单者提出撤销的同意处理
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function yq_sd_cancel_handle(PamAccount $account, PlatformStatus $platform_status)
	{
		// 撤销进行中
		// if ($platform_status->yq_order_status != Yq::ORDER_STATUS_QUASH) {
		// 	return false;
		// }
		//
		//
		// if ($platform_status->yq_cancel_status != Yq::CANCEL_STATUS_431) {
		// 	return false;
		// }
		if (!in_array($platform_status->yq_order_status, [
			Yq::ORDER_STATUS_CANCELING,
			Yq::CANCEL_STATUS_431,
			// Yq::CANCEL_STATUS_430
		])
		) {
			return false;
		}

		return true;
	}

	public function yq_star(PamAccount $account, PlatformStatus $platform_status)
	{
		// 撤销完成不显示这个功能
		if ($platform_status->yq_order_status == Yq::ORDER_STATUS_OVER) {
			return true;
		}
		return false;
	}

	/**
	 * 代练妈妈申请客服介入
	 * @param PamAccount     $account
	 * @param PlatformStatus $platform_status
	 * @return bool
	 */
	public function yq_kf(PamAccount $account, PlatformStatus $platform_status)
	{

		if (!in_array($platform_status->yq_order_status, [
			Yq::ORDER_STATUS_CANCELING,
			Yq::CANCEL_STATUS_TREATY,
			Yq::CANCEL_STATUS_431,
			Yq::ORDER_STATUS_EXCEPTION,
			Yq::kf_STATUS_420,
		])
		) {
			return false;
		}
		return true;
	}
}
