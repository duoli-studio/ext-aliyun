<?php namespace Order\Http\Api\Transformer;

use League\Fractal\TransformerAbstract;
use User\Models\PamRole;

class RoleTransformer extends TransformerAbstract
{
	public function transform(PamRole $role)
	{
		return [
			'name' => $role->name,
			'id'   => $role->id,
		];
	}
}