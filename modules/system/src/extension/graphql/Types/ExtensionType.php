<?php
namespace System\Extension\Graphql\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Abstracts\Type as AbstractType;

/**
 * Class ExtensionType.
 */
class ExtensionType extends AbstractType
{
	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'authors' => [
				'description' => '',
				'type'        => Type::string(),
			],
			'description' => [
				'description' => '',
				'type'        => Type::string(),
			],
			'enabled'        => [
				'description' => '',
				'type'        => Type::boolean(),
			],
			'identification' => [
				'description' => '',
				'type'        => Type::string(),
			],
			'initialized' => [
				'description' => '',
				'type'        => Type::boolean(),
			],
			'installed' => [
				'description' => '',
				'type'        => Type::boolean(),
			],
			'name'           => [
				'description' => '',
				'type'        => Type::string(),
			],
			'namespace'      => [
				'description' => '',
				'type'        => Type::string(),
			],
			'provider'       => [
				'description' => '',
				'type'        => Type::string(),
			],
			'requireInstall'       => [
				'description' => '',
				'type'        => Type::boolean(),
			],
			'requireUninstall'       => [
				'description' => '',
				'type'        => Type::boolean(),
			],
		];
	}

	/**
	 * @return string
	 */
	public function name()
	{
		return 'extension';
	}
}
