<?php namespace Order\Action;

use App\Models\PamAccount;

class ActionBasic {

	protected $error   = '';
	protected $success = '';

	/** @type  PamAccount */
	protected $pam;

	public function setError($error) {
		$this->error = $error;
		return false;
	}

	public function getError() {
		return $this->error;
	}

	public function setPam($pam) {
		$this->pam = $pam;
	}

	/**
	 * 获取 pam 值
	 * @param null $key
	 * @return mixed|string
	 */
	public function getPam($key = null) {
		if ($key && $this->pam && isset($this->pam->$key)) {
			return $this->pam->$key;
		} else {
			return '';
		}
	}
}