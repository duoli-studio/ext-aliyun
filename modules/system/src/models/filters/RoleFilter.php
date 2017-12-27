<?php namespace System\Models\Filters;

use EloquentFilter\ModelFilter;

class RoleFilter extends ModelFilter
{
	// This will filter 'company_id' OR 'company'
	public function type($type)
	{
		return $this->where('type', $type);
	}
}