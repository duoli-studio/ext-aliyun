<?php namespace System\Action;

use OSS\OssClient;
use System\Classes\Uploader;

class OssUploader extends Uploader
{


	/**
	 * 是否在保存后删除本地文件
	 * @var bool
	 */
	private $deleteLocal = true;

	private $saveAliyun = false;

	public function __construct(string $folder = 'uploads')
	{
		parent::__construct($folder);
		$save_type = sys_setting('extension::oss.save_type');
		if ($save_type == 'aliyun') {
			$this->saveAliyun = true;
		}
	}

	/**
	 * 保存文件输入
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
	 * @return bool|mixed
	 */
	public function saveFile($file)
	{
		if (!parent::saveFile($file)) {
			return false;
		}
		if ($this->saveAliyun) {
			return $this->saveAli($this->deleteLocal);
		}
		return true;
	}

	/**
	 * 保存流输入
	 * @param $content
	 * @return bool
	 */
	public function saveInput($content)
	{
		if (!parent::saveInput($content)) {
			return false;
		}
		if ($this->saveAliyun) {
			return $this->saveAli($this->deleteLocal);
		}
		return true;
	}

	/**
	 * 保存到阿里云
	 * @param bool $delete_local
	 * @return bool
	 */
	private function saveAli($delete_local = true)
	{
		// 设置返回地址
		$returnUrl = $this->getSetting()->get('extension::oss_aliyun.url_prefix');
		$this->setReturnUrl($returnUrl);

		if (!$returnUrl) {
			return $this->setError(trans('system::action.oss_uploader.return_url_error'));
		}

		$endpoint = $this->getSetting()->get('extension::oss_aliyun.endpoint');
		$bucket   = $this->getSetting()->get('extension::oss_aliyun.bucket');

		$accessKeyId     = $this->getSetting()->get('extension::oss_aliyun.access_key');
		$accessKeySecret = $this->getSetting()->get('extension::oss_aliyun.access_secret');
		$this->saveAli   = true;
		try {
			$ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, false);
			$ossClient->putObject($bucket, $this->destination, $this->storage()->get($this->destination));
			if ($delete_local) {
				$this->storage()->delete($this->destination);
			}
			return true;
		} catch (\Exception $e) {
			return $this->setError($e->getMessage());
		}
	}
}