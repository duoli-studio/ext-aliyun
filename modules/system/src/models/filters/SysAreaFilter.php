<?php namespace System\Models\Filters;

use EloquentFilter\ModelFilter;

class SysAreaFilter extends ModelFilter
{
	public function parent($id)
	{
		$id = intval($id);
		return $this->where('parent_id', $id);
	}
}