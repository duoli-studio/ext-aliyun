<?php namespace Poppy\Extension\Worker;

class Worker {
	private static $instance = null;

	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
	}

	public function __desctruct() {
	}

	/**
	 * @return CredentialProcessor
	 */
	public function getCredentialProcessor() {
		return \App::make('imvkmark.l5-worker.credentialprocessor');
	}

	public function outputCredential() {
		return $this->getCredentialProcessor()->outputCredential();
	}

	/**
	 * @param $credential
	 * @return bool|int
	 */
	public function validateCredential($credential) {
		$userId = $this->getCredentialProcessor()->validateCredential($credential);
		if (!$userId) {
			return false;
		}
		return $userId;
	}
}