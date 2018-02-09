<?php namespace System\Models\Filters;

use EloquentFilter\ModelFilter;

class RoleFilter extends ModelFilter
{
	public function id($id)
	{
		return $this->where('id', $id);
	}

	public function name($name)
	{
		return $this->where('name', $name);
	}

	public function type($type)
	{
		return $this->where('type', $type);
	}
}