<?php namespace User\Pam\Action;

use System\Classes\Traits\SystemTrait;
use User\Models\PamBind;

class User
{
	use SystemTrait;

	/**
	 * 解绑第三方账号
	 * @param string $type 第三方账号的类型, 暂时只支持 qq
	 * @return bool
	 */
	public function unbind($type)
	{
		if (!$this->checkPermission()) {
			return false;
		}

		$validator = \Validator::make([
			'type' => $type,
		], [
			'type' => 'required|in:qq,alipay,weixin',
		], [], [
			'type' => '第三方绑定账号类型',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		if ($type == 'qq') {
			PamBind::where('account_id', $this->pam->id)->update([
				'qq_key'      => '',
				'qq_union_id' => '',
			]);
			return true;
		}
		if ($type == 'weixin') {
			PamBind::where('account_id', $this->pam->id)->update([
				'weixin_key'      => '',
				'weixin_union_id' => '',
			]);
			return true;
		}
		return $this->setError('第三方绑定账号类型错误');
	}
}