<?php namespace User\Fans\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;

/**
 * Class SettingType.
 */
class PageType extends AbstractType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'number';
		$this->attributes['description'] = trans('user::fans.graphql.type_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [

		];
	}
}
