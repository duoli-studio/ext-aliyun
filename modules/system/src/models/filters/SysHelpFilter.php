<?php namespace System\Models\Filters;

use EloquentFilter\ModelFilter;

class SysHelpFilter extends ModelFilter
{
	public function id($id)
	{
		return $this->where('id', $id);
	}

	public function cat($cat_id)
	{
		return $this->where('cat_id', $cat_id);
	}
}