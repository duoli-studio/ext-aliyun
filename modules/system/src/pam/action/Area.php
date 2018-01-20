<?php namespace System\Pam\Action;

/**
 * 地区操作
 */
use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;
use System\Models\SysArea;


class Area
{

	use SystemTrait;

	//创建地区
	public function create($data)
	{

		if (!$this->checkPermission()) {
			return false;
		}
		$data      = json_decode($data, true);
		//验证
		$initDb    = [
			'code'     => strval(array_get($data, 'code')),
			'province' => strval(array_get($data, 'province')),
			'city'     => strval(array_get($data, 'city')),
			'district' => strval(array_get($data, 'district')),
			'parent'   => strval(array_get($data, 'parent')),
		];
		$validator = \Validator::make($initDb, [
			'code'     => [
				Rule::required(),
				Rule::String(),
			],
			'province' => [
				Rule::string(),
			],
			'city'     => [
				Rule::string(),
			],
			'district' => [
				Rule::string(),
			],
			'parent'   => [
				Rule::required(),
				Rule::integer(),
			], [], [
				'code'     => trans('system.area.db.code'),
				'province' => trans('system.area.db.province'),
				'city'     => trans('system.area.db.city'),
				'district' => trans('system.area.db.district'),
				'parent'   => trans('system.area.db.parent'),
			],
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}
		//验证表中是否有存在重复数据
		try {
			//数据库操作
			SysArea::create($initDb);
			return true;
		} catch (\Exception $e) {
			return $this->setError($e->getMessage());
		}
	}

	//修改地区
	public function edit($id, $data)
	{
		//json
		$data      = json_decode($data, true);
		$array     = [
			'code'     => strval(array_get($data, 'code')),
			'province' => strval(array_get($data, 'province')),
			'city'     => strval(array_get($data, 'city')),
			'district' => strval(array_get($data, 'district')),
			'parent'   => strval(array_get($data, 'parent')),
		];
		$validator = \Validator::make($array, [
			'code'     => [
				Rule::required(),
				Rule::String(),
			],
			'province' => [
				Rule::string(),
			],
			'city'     => [
				Rule::string(),
			],
			'district' => [
				Rule::string(),
			],
			'parent'   => [
				Rule::required(),
				Rule::integer(),
			], [], [
				'code'     => trans('system.area.db.code'),
				'province' => trans('system.area.db.province'),
				'city'     => trans('system.area.db.city'),
				'district' => trans('system.area.db.district'),
				'parent'   => trans('system.area.db.parent'),
			],
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}
		//
		SysArea::where('id', $id)->update([
			'code'     => $array['code'],
			'province' => $array['province'],
			'city'     => $array['city'],
			'district' => $array['district'],
			'parent'   => $array['parent'],
		]);
		return true;
	}
	//删除地区
	public function destroy($id){
		//判断是否为父级
		if(SysArea::where('parent', $id)->exists()){
			return $this->setError('该地区下还有子地区, 无法删除, 请删除子级别地区之后再进行操作');
		}
		SysArea::destroy($id);
		return true;
	}
}