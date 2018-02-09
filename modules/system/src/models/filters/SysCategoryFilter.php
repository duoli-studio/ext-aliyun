<?php namespace System\Models\Filters;

use EloquentFilter\ModelFilter;

class SysCategoryFilter extends ModelFilter
{
	public function id($id)
	{
		return $this->where('id', $id);
	}

	public function type($type)
	{
		return $this->where('type', $type);
	}
}