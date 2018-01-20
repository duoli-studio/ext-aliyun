<?php namespace System\Models\Filters;

use EloquentFilter\ModelFilter;

class RoleFilter extends ModelFilter
{
	public function type($type)
	{
		return $this->where('type', $type);
	}
}