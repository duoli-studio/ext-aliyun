<?php namespace System\Action;

use System\Classes\Traits\SystemTrait;
use System\Models\PamBind;

class OAuth
{
	use SystemTrait;

	/**
	 * 解绑第三方账号
	 * @param string $type 第三方账号的类型
	 * @return bool
	 */
	public function unbind($type)
	{
		if (!$this->checkPam()) {
			return false;
		}

		$validator = \Validator::make([
			'type' => $type,
		], [
			'type' => 'required|in:qq,wx',
		], [], [
			'type' => trans('system::action.o_auth.bind_type'),
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
		if ($type == 'wx') {
			PamBind::where('account_id', $this->pam->id)->update([
				'wx_key'      => '',
				'wx_union_id' => '',
			]);

			return true;
		}

		return $this->setError(trans('system::action.o_auth.bind_type_error'));
	}
}