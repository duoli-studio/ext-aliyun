<?php namespace Order\Application\Platform\Traits;

use App\Lemon\Repositories\Sour\LmStr;
use App\Models\PlatformAccount;
use App\Models\PlatformStatus;
use App\Models\PlatformSyncLog;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Support\Collection;

trait BaseTraits {

	protected $error = '';

	/**
	 * 设置错误信息, 记录错误日志
	 * @param       $error
	 * @param bool  $log 是否记录日志
	 * @return bool
	 */
	public function setError($error, $log = false) {
		$order_id = $this->order->order_id;
		if ($error instanceof MessageBag) {
			$messages = '';
			foreach ($error->all(':message') as $message) {
				$messages .= $message . '<br>';
			}
			//		if (\Request::ajax() || $isJson) {
			//			$messages = '';
			//			foreach ($message->all(':message') as $message) {
			//				$messages .= $message . ',';
			//			}
			//			$messages = rtrim($messages, ',');
			//		}
			$error = rtrim($messages, '<br>');
		}
		if (isset($this->data) && $this->data) {
			$requestCode = json_encode($this->data);
		} else {
			$requestCode = '';
		}
		if ($log) {
			PlatformSyncLog::error($this->platform, $error, $order_id, $requestCode);
		}
		$this->error = $error;
		return false;
	}

	/**
	 * 获取错误
	 * @return string
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * 记录成功信息
	 * @param $message
	 * @return bool
	 */
	public function logSuccess($message) {
		if (isset($this->data) && $this->data) {
			$requestCode = json_encode($this->data);
		} else {
			$requestCode = '';
		}
		PlatformSyncLog::success($this->platform, $message, $this->order->order_id, $requestCode);
		return true;
	}


	/**
	 * 检测账号是否存在， 检测账号和发单平台是否匹配， 检测订单是否存在
	 * @return bool
	 * @throws \Exception
	 */
	public function checkEnv() {
		if (!$this->platformAccount) {
			throw new \Exception('发单账号不存在');
		}
		if ($this->platformAccount->platform != $this->platform) {
			throw new \Exception('发单平台不匹配, 应该发单平台为 ' . PlatformAccount::kvPlatform($this->platform) . ' 实际账号平台是 ' . PlatformAccount::kvPlatform($this->platformAccount->platform));
		}
		if (!$this->order) {
			throw new \Exception('订单原始数据不存在');
		}
	}


	/**
	 * 保存已经发布的订单的状态, 用于发布页面
	 */
	protected function saveAssignAccount() {
		/** @type Collection $platformStatus */
		$platformStatus = PlatformStatus::where('order_id', $this->order->order_id)->get();
		if (!$platformStatus->isEmpty()) {
			$platformAccount = [];
			foreach ($platformStatus as $status) {
				if ($status->platform == PlatformAccount::PLATFORM_MAO) {
					if ($status->mao_is_publish && !$status->mao_is_delete) {
						$platformAccount[] = $status->pt_account_id;
					}
				}
				if ($status->platform == PlatformAccount::PLATFORM_YQ) {
					if ($status->yq_is_publish && !$status->yq_is_delete) {
						$platformAccount[] = $status->pt_account_id;
					}
				}
				if ($status->platform == PlatformAccount::PLATFORM_YI) {
					if ($status->yi_is_publish && !$status->yi_is_delete) {
						$platformAccount[] = $status->pt_account_id;
					}
				}
				if ($status->platform == PlatformAccount::PLATFORM_BAOZI) {
					if ($status->baozi_is_publish && !$status->baozi_is_delete) {
						$platformAccount[] = $status->pt_account_id;
					}
				}
				if ($status->platform == PlatformAccount::PLATFORM_TONG) {
					if ($status->tong_is_publish && !$status->tong_is_delete) {
						$platformAccount[] = $status->pt_account_id;
					}
				}
				if ($status->platform == PlatformAccount::PLATFORM_MAMA) {
					if ($status->mama_is_publish && !$status->mama_is_delete) {
						$platformAccount[] = $status->pt_account_id;
					}
				}
			}
			if ($platformAccount) {
				$fieldIn = LmStr::matchEncode($platformAccount);

				$this->order->publish_account = $fieldIn;
				$this->order->save();
			}
		}
	}


	/**
	 * 发单时候的自有检测
	 * @return bool
	 */
	protected function checkPublish() {
		$validator = \Validator::make($this->order->toArray(), [
			'order_hours' => 'required',
		], [
			'order_hours.required' => '代练时限不能为空',
		]);

		if ($validator->fails()) {
			return $this->setError($validator->errors());
		}
		if (bccomp($this->order->order_price, 10) < 0) {
			return $this->setError('代练费用不能少于 10 元');
		}
		if (bccomp($this->order->order_safe_money, 5) < 0) {
			return $this->setError('安全保证金不能少于 5 元');
		}
		if (bccomp($this->order->order_speed_money, 5) < 0) {
			return $this->setError('效率保证金不能少于 5 元');
		}
		return true;
	}

	protected function createStatus() {
		return PlatformStatus::firstOrCreate([
			'order_id'      => $this->order->order_id,
			'platform'      => $this->platform,
			'pt_account_id' => $this->platformAccount->id,
		]);
	}

	/**
	 * @param $pt_account_id
	 * @return PlatformAccount
	 */
	protected function getPtAccount($pt_account_id) {
		return PlatformAccount::getCacheItem($pt_account_id);
	}
}