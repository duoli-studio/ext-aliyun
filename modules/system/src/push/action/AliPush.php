<?php namespace System\Push\Action;

use Poppy\Extension\Aliyun\Core\Config;
use Poppy\Extension\Aliyun\Core\DefaultAcsClient;
use Poppy\Extension\Aliyun\Core\Profile\DefaultProfile;
use Poppy\Extension\Aliyun\Core\Regions\EndpointConfig;
use Poppy\Extension\Aliyun\Push\Request\V20160801\BindTagRequest;
use Poppy\Extension\Aliyun\Push\Request\V20160801\PushRequest;
use Poppy\Framework\Exceptions\DoException;

class AliPush
{
	static $acsClient = null;
	/**
	 * @var DefaultAcsClient
	 */
	protected static $instance;

	/**
	 * Create a new instance of this singleton.
	 * @return DefaultAcsClient
	 * @throws \Exception
	 */
	final static public function instance()
	{
		return isset(static::$instance)
			? static::$instance
			: static::$instance = self::initAcsClient();
	}

	/**
	 * send push notice
	 * PushNotice To Android Document
	 * @url https://help.aliyun.com/document_detail/30082.html
	 * @param $notify
	 * @return bool
	 * @throws \Exception
	 */
	public static function sendPush($notify)
	{
		$request = new PushRequest();
		$request->setAppKey(app('setting')->get('extension::push.app_key'));
		$request->setDeviceType("ANDROID");
		// 消息类型:  MESSAGE  NOTICE
		$request->setPushType("NOTICE");
		$request->setTitle($notify['title']);
		$request->setBody($notify['content']);
		$extras = json_encode($notify['extras']);
		$request->setAndroidExtParameters($extras);
		$request->setiOSExtParameters($extras);
		$request->setStoreOffline("true");
		switch ($notify['broadcast_type']) {
			case 'DEVICE':
			default:
				$request->setTarget('DEVICE');
				if (isset($notify['registration_ids']) && $notify['registration_ids']) {
					// 根据 Target 来设定，如 Target=device, 则对应的值为 设备id1,设备id2. 多个值使用逗号分隔.(帐号与设备有一次最多100个的限制)
					$request->setTargetValue(implode(',', $notify['registration_ids']));
				}
				break;
			case 'ALL':
				$request->setTarget('ALL');
				$request->setTargetValue('ALL');
				break;
		}
		self::instance()->getAcsResponse($request);
		return true;

	}

	/**
	 * @param $tag
	 * @param $client_key
	 * @throws \Exception
	 */
	public function bindTag($tag, $client_key)
	{
		$request = new BindTagRequest();
		$request->setAppKey('24787617');
		$request->setTagName($tag);
		$request->setClientKey($client_key);
		$request->setKeyType(1);
		self::instance()->getAcsResponse($request);
	}

	/**
	 * @return DefaultAcsClient
	 * @throws DoException
	 */
	static protected function initAcsClient()
	{
		if (!app('setting')->get('extension::push.is_open')) {
			throw new DoException('App 通知尚未开启');
		}
		$accessKeyId     = app('setting')->get('extension::push.access_key_id');
		$accessKeySecret = app('setting')->get('extension::push.access_key_secret');
		try {
			if (static::$acsClient == null) {
				Config::load();
				EndpointConfig::load();
				$iClientProfile    = DefaultProfile::getProfile("cn-hangzhou", $accessKeyId, $accessKeySecret);
				static::$acsClient = new DefaultAcsClient($iClientProfile);
			}
			return static::$acsClient;
		} catch (\Exception $e) {
			throw new DoException($e->getMessage());
		}
	}

}