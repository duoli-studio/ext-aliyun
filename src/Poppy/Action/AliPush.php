<?php namespace Poppy\Extension\Aliyun\Poppy\Action;

use Poppy\Extension\Aliyun\Core\Config;
use Poppy\Extension\Aliyun\Core\DefaultAcsClient;
use Poppy\Extension\Aliyun\Core\Profile\DefaultProfile;
use Poppy\Extension\Aliyun\Poppy\Jobs\AliPushJob;
use Poppy\Extension\Aliyun\Push\Request\V20160801\BindTagRequest;
use Poppy\Extension\Aliyun\Push\Request\V20160801\PushRequest;
use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Framework\Exceptions\DoException;
use Poppy\Framework\Helper\StrHelper;
use Poppy\Framework\Validation\Rule;
use Throwable;
use Validator;

/**
 * @url https://help.aliyun.com/document_detail/30082.html
 */
class AliPush
{

	use AppTrait;

	/**
	 * 发送的信息
	 * @var array
	 */
	private $params;
	/**
	 * @var array 发送记录的信息
	 */
	private $messages = [];
	/**
	 * @var string
	 */
	private $androidAppKey;
	/**
	 * @var string
	 */
	private $iosAppKey;
	/**
	 * @var int
	 */
	private $cutNum = 100;
	/**
	 * @var DefaultAcsClient
	 */
	private static $acsClient;
	/**
	 * @var DefaultAcsClient
	 */
	private static $instance;

	public function __construct()
	{
		$this->androidAppKey = sys_setting('addon::ali_push.android_app_key');
		$this->iosAppKey     = sys_setting('addon::ali_push.ios_app_key');
	}

	/**
	 * 发送
	 * @param array $params 参数
	 * @return bool
	 */
	public function send($params): bool
	{
		if (empty($params)) {
			return true;
		}

		$registrationIds = $params['registration_ids'];

		$this->params                   = $params;
		$this->params['broadcast_type'] = strtolower($params['broadcast_type'] ?? '');
		$this->params['device_type']    = strtolower($params['device_type'] ?? 'android|notice;ios|notice');

		if (!$this->checkParams()) {
			sys_error('addon', self::class, ['error' => 'checkParams错误', 'params' => $this->params]);
			return false;
		}

		$devices = StrHelper::parseKey($this->params['device_type']);

		if (!count($devices)) {
			return $this->setError('未指定发送设备以及发送类型');
		}

		// send ios
		$sendIosType        = $devices['ios'] ?? '';
		$iosRegistrationIds = $registrationIds['ios'] ?? [];
		if ($sendIosType) {
			if ($iosRegistrationIds) {
				if (count($iosRegistrationIds) > $this->cutNum) {
					// 重发 IOS
					$currentRegistrationIds           = array_splice($iosRegistrationIds, 0, $this->cutNum);
					$this->params['registration_ids'] = [
						'ios' => $iosRegistrationIds,
					];

					$params = $this->params;
					unset($params['registration_ids']['android']);
					dispatch(new AliPushJob($params));
				}
				else {
					$currentRegistrationIds = $iosRegistrationIds;
				}

				if (!$this->checkSendType($sendIosType)) {
					return false;
				}
				$this->params['ios_registration_ids'] = $currentRegistrationIds;
			}

			if ($this->params['broadcast_type'] !== 'device' || !empty($this->params['ios_registration_ids'])) {
				$this->params['ios_type'] = $sendIosType;
				if (!$this->sendIos()) {
					return false;
				}
			}
		}

		// send android
		$sendAndroidType        = $devices['android'] ?? '';
		$androidRegistrationIds = $registrationIds['android'] ?? [];
		if ($sendAndroidType) {
			$channel = sys_setting('addon::ali_push.android_channel');
			if (!$channel) {
				return $this->setError('Android 推送 8.0 以上版本需要设置通道才能发送');
			}
			if ($androidRegistrationIds) {
				if (count($androidRegistrationIds) > $this->cutNum) {
					// 重发 android
					$currentRegistrationIds           = array_splice($androidRegistrationIds, 0, $this->cutNum);
					$this->params['registration_ids'] = [
						'android' => $androidRegistrationIds,
					];

					$params = $this->params;
					unset($params['registration_ids']['ios']);
					dispatch(new AliPushJob($params));
				}
				else {
					$currentRegistrationIds = $androidRegistrationIds;
				}

				if (!$this->checkSendType($sendAndroidType)) {
					return false;
				}
				$this->params['android_registration_ids'] = $currentRegistrationIds;
			}

			if ($this->params['broadcast_type'] !== 'device' || !empty($this->params['android_registration_ids'])) {
				$this->params['android_type'] = $sendAndroidType;
				if (!$this->sendAndroid()) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * @param string $device_type 设备类型 [ANDROID|IOS]
	 * @param string $tag         标签
	 * @param string $client_key  客户端代码
	 * @return bool
	 */
	public function bindTag($device_type, $tag, $client_key)
	{
		$request = new BindTagRequest();
		if ($device_type === 'android') {
			$request->setAppKey($this->androidAppKey);
		}
		else {
			$request->setAppKey($this->iosAppKey);
		}
		$request->setTagName($tag);
		$request->setClientKey($client_key);
		$request->setKeyType('DEVICE'); // ClientKey的类型，device(1)，account(2)，alias(3)
		try {
			$this->messages[] = [
				'type'    => 'tag',
				'message' => self::initAcsClient()->getAcsResponse($request),
			];
			return true;
		} catch (Throwable $e) {
			return $this->setError($e->getMessage());
		}
	}

	/**
	 * 获取信息
	 * @return array
	 */
	public function getMessages(): array
	{
		return $this->messages;
	}

	/**
	 * 获取实例
	 * @return self
	 */
	final public static function getInstance(): self
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * 发送 IOS 设备
	 * @return bool
	 */
	private function sendIos()
	{
		$params = $this->params;
		if (!sys_setting('addon::ali_push.ios_is_open')) {
			return $this->setError('IOS 设备未开启推送');
		}

		$request = $this->initRequest($params);
		$extras  = json_encode($params['extras'] ?? []);
		$request->setiOSExtParameters($extras);

		// ios Env
		$iosApnsEnv = is_production() ? 'PRODUCT' : 'DEV';
		$request->setiOSApnsEnv($iosApnsEnv);

		// push type
		$request->setPushType(strtoupper($params['ios_type']));

		$message = [
			'type'    => 'ios',
			'request' => $request->getQueryParameters(),
		];
		try {
			$this->messages[] = $message + [
					'message' => self::initAcsClient()->getAcsResponse($request),
				];
			// 发送完成， 清空发送数据
			$this->params['ios_registration_ids'] = [];
			return true;
		} catch (Throwable $e) {
			sys_error('addon', self::class, ['error' => 'sendIos错误：' . $e->getMessage()]);
			$this->messages[] = $message + [
					'message' => $e->getMessage(),
				];
			return $this->setError($e->getMessage());
		}
	}

	/**
	 * 发送 Android 设备
	 * @return bool
	 */
	private function sendAndroid()
	{
		$params = $this->params;
		if (!sys_setting('addon::ali_push.android_is_open')) {
			return $this->setError('Android 设备未开启推送');
		}

		$request = $this->initRequest($params, 'android');
		$extras  = json_encode($params['extras'] ?? []);
		$request->setAndroidExtParameters($extras);
		$channel = sys_setting('addon::ali_push.android_channel');
		if ($channel) {
			$request->setAndroidNotificationChannel($channel);
		}
		// android 消息中没有返回 extras , 这里使用 content 替代 extras 返回
		if ($params['android_type'] === 'message') {
			$request->setBody($extras);
		}
		// push type
		$request->setPushType(strtoupper($params['android_type']));
		$message = [
			'type'    => 'android',
			'request' => $request->getQueryParameters(),
		];
		try {
			$this->messages[] = $message + [
					'message' => self::initAcsClient()->getAcsResponse($request),
				];
			// 发送完成， 清空发送数据
			$this->params['android_registration_ids'] = [];
			return true;
		} catch (Throwable $e) {
			sys_error('addon', self::class, ['error' => 'sendAndroid错误：' . $e->getMessage()]);
			$this->messages[] = $message + [
					'message' => $e->getMessage(),
				];
			return $this->setError($e->getMessage());
		}
	}

	/**
	 * 检测发送参数
	 * @return bool
	 */
	private function checkParams(): bool
	{
		$validator = Validator::make($this->params, [
			// 设备类型
			'broadcast_type' => [
				Rule::required(),
				Rule::in(['device', 'all', 'tag']),
			],
			'android_type'   => [
				Rule::in(['notice', 'message']),
			],
			'ios_type'       => [
				Rule::in(['notice', 'message']),
			],
			'title'          => [
				Rule::required(),
			],
			'content'        => [
				Rule::required(),
			],
			'offline'        => [
				Rule::required(),
				Rule::in(['Y', 'N']),
			],
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		if (($this->params['broadcast_type'] === 'device') && !count($this->params['registration_ids'] ?? [])) {
			return $this->setError('用户设备号不能为空');
		}
		if (($this->params['broadcast_type'] === 'tag') && !count($this->params['registration_tags'] ?? [])) {
			return $this->setError('用户标签不能为空');
		}
		return true;
	}

	/**
	 * 发送推送类型[通知/消息]
	 * @param string $type
	 * @return bool
	 */
	private function checkSendType($type): bool
	{
		if (!in_array($type, [
			'message', 'notice',
		], false)) {
			return $this->setError("推送类型 {$type} 不允许, 仅仅支持 message/notice ");
		}
		return true;
	}


	/**
	 * @param array  $params      初始化参数
	 * @param string $device_type 设备类型
	 * @return PushRequest
	 */
	private function initRequest($params, $device_type = 'ios'): PushRequest
	{
		$request = new PushRequest();
		if ($device_type === 'ios') {
			$request->setDeviceType('iOS');
			$request->setAppKey($this->iosAppKey);
		}
		else {
			$request->setDeviceType('ANDROID');
			$request->setAppKey($this->androidAppKey);
		}
		switch ($params['broadcast_type']) {
			// 根据 Target 来设定，如 Target=device, 则对应的值为 设备id1,设备id2. 多个值使用逗号分隔.(帐号与设备有一次最多100个的限制)
			case 'device':
			default:
				$request->setTarget('DEVICE');
				if ($device_type === 'ios') {
					$request->setTargetValue(implode(',', $params['ios_registration_ids']));
				}
				if ($device_type === 'android') {
					$request->setTargetValue(implode(',', $params['android_registration_ids']));
				}
				break;
			case 'all':
				$request->setTarget('ALL');
				$request->setTargetValue('ALL');
				break;
			case 'tag':
				$request->setTarget('TAG');
				$request->setTargetValue($params['registration_tags']);
				break;
		}
		$request->setTitle($params['title']);
		$request->setBody($params['content']);
		return $request;
	}

	/**
	 * 初始化
	 * @return DefaultAcsClient
	 * @throws DoException
	 */
	private static function initAcsClient()
	{
		$accessKeyId     = sys_setting('addon::ali_push.access_key');
		$accessKeySecret = sys_setting('addon::ali_push.access_secret');

		try {
			if (self::$acsClient === null) {
				Config::load();
				$iClientProfile  = DefaultProfile::getProfile('cn-hangzhou', $accessKeyId, $accessKeySecret);
				self::$acsClient = new DefaultAcsClient($iClientProfile);
			}
			return self::$acsClient;
		} catch (Throwable $e) {
			sys_error('addon', self::class, ['error' => 'initAcsClient错误：' . $e->getMessage()]);
			throw new DoException($e->getMessage());
		}
	}
}