<?php
use App\Lemon\Dailian\Action\ActionPlatformOrder;
use App\Models\BaseConfig;
use App\Models\PlatformAccount;
use App\Models\PlatformOrder;


function order_tag_decode($tag)
{
	if ($tag) {
		return str_replace('|', ', ', trim($tag, '|'));
	}
	else {
		return '';
	}
}

function order_tag_encode($tag)
{
	if ($tag) {
		return '|' . implode('|', $tag) . '|';
	}
	else {
		return '';
	}
}

/**
 * 获取头像地址
 * @param        $head_pic
 * @param string $size
 * @return string
 */

function avatar($head_pic, $size = 'middle')
{
	if (!$head_pic) {
		$avatar = config('app.url_image') . "/1dailian/avatar." . $size . ".jpg";
	}
	else {
		$avatar = $head_pic;
	}
	return $avatar;
}






function js_global($url = '')
{
	$url       = $url ?: config('app.url');
	$url_js    = $url . '/assets/js';
	$url_image = $url . '/assets/image';

	$cookie_prefix = config('app.cookie_prefix');
	$cookie_path   = config('app.cookie_path');
	$cookie_domain = config('app.cookie_domain');

	$supportUrl     = [
		'game_server_html' => route('support_game.server_html'),
		'game_type_html'   => route('support_game.type_html'),
	];
	$supportUrlJson = json_encode($supportUrl);


	$uploadUrl = route('support_upload.image');

	$js = <<<JS
	define(function(){
		return {
		    cookie_domain : ".{$cookie_domain}",
		    cookie_path : "{$cookie_path}",
		    cookie_prefix : "{$cookie_prefix}",
		    url_site : "{$url}",
		    url_js : "{$url_js}",
		    url_image : "{$url_image}",
		    support_url  :  {$supportUrlJson},
		    upload_url  :  "{$uploadUrl}"
		}
	})
JS;
	return str_replace(["\n", "\/", " ", "\t"], ['', '/', '', ''], $js);
}


/**
 * @param        $status \App\Models\PlatformStatus
 * @param string $type
 * @return string
 */
function pt_status_class($status, $type = 'yi')
{
	$publish = $type . '_is_publish';
	$delete  = $type . '_is_delete';
	$error   = $type . '_is_error';
	if ($status->$publish) {
		if ($status->$delete || $status->$error) {
			return 'text-danger';
		}
		return 'text-info';
	}
	return 'text-danger';
}

/**
 * 发送短信
 * @param $api_mark
 * @param $mobile
 * @param $content
 * @return mixed
 */
function send_sms($api_mark, $mobile, $content)
{
	$createSms = function ($api_type) {
		$type = config('l5-sms.sms.' . $api_type . '.type');
		if (!$type) {
			throw new \Exception('短信类型不存在, 无法进行短信发送操作, 请在配置文件中设置!');
		}
		$config     = config('l5-sms.sms.' . $api_type);
		$type       = ucfirst(camel_case($type));
		$class      = 'Imvkmark\\L5Sms\\Repositories\\' . $type;
		$formatConf = [
			'public_key' => isset($config['public_key']) ? $config['public_key'] : '',
			'password'   => isset($config['password']) ? $config['password'] : '',
			'sign'       => isset($config['sign']) ? $config['sign'] : '',
			'type'       => $type,
		];
		$sms        = new $class($formatConf);
		return $sms;
	};
	/** @var \Imvkmark\L5Sms\Contracts\Sms $Sms */
	$Sms = $createSms($api_mark);
	return $Sms->send($mobile, $content);
}


/**
 * 同步订单
 * @param int   $order_id
 * @param array $log
 */
function pt_sync_order($order_id, &$log = [])
{
	$Order = new ActionPlatformOrder($order_id);

	// 员工接单不必进行同步详细
	if ($Order->getOrder()->employee_publish) {
		return;
	}
	// 订单已经删除, 此处不进行处理
	if ($Order->getOrder()->order_status == PlatformOrder::ORDER_STATUS_DELETE) {
		return;
	}

	$Order->syncDetail();
	$Order->syncAccept();
	/** @type PlatformOrder $order */
	$order = PlatformOrder::with('platformStatus')->find($order_id);

	// 全部删除之后可以重新再进行编辑操作
	if ($order->platformStatus) {
		$allDestroy = true;
		foreach ($order->platformStatus as $status) {
			if ($status->platform == PlatformAccount::PLATFORM_YI && !$status->yi_is_delete) {
				$allDestroy = false;
			}
			if ($status->platform == PlatformAccount::PLATFORM_YQ && !$status->yq_is_delete) {
				$allDestroy = false;
			}
			if ($status->platform == PlatformAccount::PLATFORM_MAO && !$status->mao_is_delete) {
				$allDestroy = false;
			}
			if ($status->platform == PlatformAccount::PLATFORM_MAMA && !$status->mama_is_delete) {
				$allDestroy = false;
			}
			if ($status->platform == PlatformAccount::PLATFORM_TONG && !$status->tong_is_delete) {
				$allDestroy = false;
			}
			if ($status->platform == PlatformAccount::PLATFORM_BAOZI && !$status->baozi_is_delete) {
				$allDestroy = false;
			}
		}
		if ($allDestroy && $order->order_status == PlatformOrder::ORDER_STATUS_REFUND) {
			$order->order_status = PlatformOrder::ORDER_STATUS_CREATE;
			$order->save();
		}
	}
	$log = $Order->getReqLog();
}