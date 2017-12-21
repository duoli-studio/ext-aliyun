<?php namespace Order\Action;

use App\Lemon\Repositories\Application\Traits\AppTrait;
use Intervention\Image\Constraint;
use OSS\Core\OssException;
use OSS\OssClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ActionAliOss
{

	use AppTrait;

	protected $destination = '';

	protected $disk = 'public_uploads';

	private $allowedExtensions = ['zip'];

	protected static $extensions = [
		'image' => [
			'extension'   => 'jpg,jpeg,png',
			'description' => '请选择图片',
		],
		'zip'   => [
			'extension'   => 'zip',
			'description' => '选择压缩包',
		],
		'rp'    => [
			'extension'   => 'rp',
			'description' => '请选择 Rp 文件',
		],
		'rplib' => [
			'extension'   => 'rplib',
			'description' => '请选择原型组件',
		],
		'bak'   => [
			'extension'   => 'bak',
			'description' => '请选择bak文件',
		],
	];

	private $saveAli = false;

	public function setExtension($extension = [])
	{
		$this->allowedExtensions = $extension;
	}

	/**
	 * 获取本地磁盘存储
	 * @return \Illuminate\Filesystem\FilesystemAdapter
	 */
	public function storage()
	{
		return \Storage::disk($this->disk);
	}

	/**
	 * 保存文件, 保存到某开发者下面
	 * @param UploadedFile $file
	 * @param string       $aim_path 文件存储路径, 不需要填写存储文件夹的目录
	 * @return mixed
	 */
	public function saveLocal(UploadedFile $file, $aim_path = '')
	{
		if ($file->isValid()) {
			// 存储
			if ($file->getClientOriginalExtension() && !in_array(strtolower($file->getClientOriginalExtension()), $this->allowedExtensions)) {
				return $this->setError('你只允许上传 "' . implode(',', $this->allowedExtensions) . '" 格式');
			}

			// 磁盘对象
			$Disk = $this->storage();

			if ($aim_path) {
				if (!(strpos($aim_path, '/') === false || strpos($aim_path, '\\') === false)) {
					return $this->setError('不允许在上传根目录存放文件');
				}
				/**
				 * 'dirname' => 'avatar',
				 * 'basename' => '265.png',
				 * 'extension' => 'png',
				 * 'filename' => '265',
				 */
				$pathInfo         = pathinfo($aim_path);
				$imageExtension   = $pathInfo['extension'];
				$fileName         = $pathInfo['filename'] . '.' . $imageExtension;
				$fileRelativePath = $pathInfo['dirname'] . '/' . $fileName;
			}
			else {
				$imageExtension   = $file->getClientOriginalExtension() ?: 'png';
				$fileName         = date('is') . str_random(8) . '.' . $imageExtension;
				$fileRelativePath = date("Ym", time()) . '/' . date("d") . '/' . date("H") . '/' . $fileName;
			}

			// 保存的完整相对路径
			$fileSaveDestination = $fileRelativePath;

			$zipContent = file_get_contents($file);
			$Disk->put($fileSaveDestination, $zipContent);


			// 缩放图片
			if ($file->getClientOriginalExtension() != 'gif') {
				$Image = \Image::make(disk_path($this->disk) . $fileSaveDestination);
				$Image->resize(1920, null, function(Constraint $constraint) {
					$constraint->aspectRatio();
					$constraint->upsize();
				});
				$Image->save(null, 60);
			}
			$this->destination = $fileRelativePath;
			return true;
		}
		else {
			return $this->setError($file->getErrorMessage());
		}
	}

	public function saveAli($delete_local = true)
	{
		$accessKeyId     = config('oss.app_key');
		$accessKeySecret = config('oss.app_secret');
		$endpoint        = config('oss.endpoint');
		$bucket          = config('oss.bucket_name');
		$this->saveAli   = true;
		try {
			$ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, false);
			$ossClient->putObject($bucket, $this->destination, $this->storage()->get($this->destination));
			if ($delete_local) {
				$this->storage()->delete($this->destination);
			}
			return true;
		} catch (OssException $e) {
			return $this->setError($e->getMessage());
		}
	}

	public function saveFile(UploadedFile $file)
	{
		$accessKeyId       = config('oss.app_key');
		$accessKeySecret   = config('oss.app_secret');
		$endpoint          = config('oss.endpoint');
		$bucket            = config('oss.bucket_name');
		$this->saveAli     = true;
		$fileName          = date('is') . str_random(8) . '.' . $file->getClientOriginalName();
		$fileRelativePath  = date("Ym", time()) . '/' . date("d") . '/' . date("H") . '/' . $fileName;
		$this->destination = $fileRelativePath;
		try {
			$ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, false);
			$ossClient->uploadFile($bucket, $this->destination, $file);
			return true;
		} catch (OssException $e) {
			return $this->setError($e->getMessage());
		}
	}

	public function getDestination()
	{
		return $this->destination;
	}

	/**
	 * 类型
	 * @param        $type
	 * @param string $return_type
	 * @return array
	 */
	public static function type($type, $return_type = 'ext_string')
	{
		if (!isset(self::$extensions[$type])) {
			$ext = self::$extensions['image'];
		}
		else {
			$ext = self::$extensions[$type];
		}
		switch ($return_type) {
			case 'desc':
				return $ext['description'];
				break;

			case 'ext_array':
				return explode(',', $ext['extension']);
				break;
			case 'ext_string':
			default:
				return $ext['extension'];
				break;
		}
	}

	/**
	 * 图片url的地址
	 * @return string
	 */
	public function getUrl()
	{
		if ($this->saveAli) {
			return config('oss.return_url') . '/' . $this->destination;
		}
		else {
			return config('app.url_upload') . '/' . $this->destination;
		}
	}
}