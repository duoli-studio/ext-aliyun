<?php namespace Order\Action;

use Intervention\Image\Constraint;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ActionImage extends ActionBasic {


	protected $destination = '';

	protected $disk = 'public_image';

	/**
	 * 保存文件, 保存到某开发者下面
	 * @param UploadedFile $file
	 * @param string       $aim_path 文件存储路径, 不需要填写存储文件夹的目录
	 * @return mixed
	 */
	public function save(UploadedFile $file, $aim_path = '') {
		if ($file->isValid()) {
			// 存储
			$allowedExtensions = [
				'png',
				'jpg',
				'gif',
				'jpeg',   // android default
			];
			if ($file->getClientOriginalExtension() && !in_array($file->getClientOriginalExtension(), $allowedExtensions)) {
				return $this->setError('你只允许上传 "' . implode(',', $allowedExtensions) . '" 格式');
			}

			// 磁盘对象
			$Disk = \Storage::disk($this->disk);

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
				$pathInfo  = pathinfo($aim_path);
				$extension = $pathInfo['extension'];
				if (!in_array($extension, $allowedExtensions)) {
					$imageExtension = 'png';
				} else {
					$imageExtension = $extension;
				}
				$imageName         = $pathInfo['filename'] . '.' . $imageExtension;
				$imageRelativePath = $pathInfo['dirname'] . '/' . $imageName;
			} else {
				$imageExtension    = $file->getClientOriginalExtension() ?: 'png';
				$imageName         = date('is') . str_random(8) . '.' . $imageExtension;
				$imageRelativePath = date("Ym", time()) . '/' . date("d") . '/' . date("H") . '/' . $imageName;
			}

			// 保存的完整相对路径
			$imageSaveDestination = $imageRelativePath;

			$imageContent = file_get_contents($file);
			$Disk->put($imageSaveDestination, $imageContent);

			/**
			 * 图片的实际存储地址
			 */
			$imageRealPath = disk_path($this->disk) . $imageSaveDestination;

			// 缩放图片
			if ($file->getClientOriginalExtension() != 'gif') {
				$Image = \Image::make($imageRealPath);
				$Image->resize(1440, null, function (Constraint $constraint) {
					$constraint->aspectRatio();
					$constraint->upsize();
				});
				$Image->save();
			}

			// 保存图片
			$this->destination = $imageRelativePath;
			return true;
		} else {
			return $this->setError($file->getErrorMessage());
		}
	}

	public function getDestination() {
		return $this->destination;
	}

	/**
	 * 图片url的地址
	 * @return string
	 */
	public function getUrl() {
		return config('app.url_upload') . '/' . $this->destination;
	}


}