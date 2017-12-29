<?php

return [
	'schema'            => 'default',
	'schemas'           => [
		'default' => [
			'mutation' => [],
			'query'    => [
				\System\Setting\Graphql\Queries\SettingsQuery::class,
			],
		],
		'backend' => [
			'mutation' => [
				\System\Setting\Graphql\Mutation\SettingMutation::class,
				\System\Pam\GraphQL\Mutation\RoleMutation::class,
				\Order\Game\GraphQL\Mutation\ServerMutation::class,
			],
			'query'    => [
				\System\Setting\Graphql\Queries\SettingQuery::class,
				\System\Setting\Graphql\Queries\SettingsQuery::class,
				\System\Pam\Graphql\Queries\RoleQuery::class,
				\Order\game\Graphql\Queries\ServerQuery::class,
			],
		],
	],
	'middleware_schema' => [
		'default' => ['cross'],
		'backend' => ['auth:api', 'cross'],
	],
	'types'             => [
		/* query
		 -------------------------------------------- */
		// config
		\System\Setting\GraphQL\Types\SettingType::class,

		// resp
		\System\Setting\GraphQL\Types\RespType::class,

		/* role
		 -------------------------------------------- */
		\System\Pam\Graphql\Input\RoleFilterType::class,
		\System\Pam\GraphQL\Types\RoleType::class,
		\System\Pam\GraphQL\Types\RoleGuardType::class,

		/* server
		 -------------------------------------------- */
		\Order\Game\GraphQL\Types\ServerType::class,

	],
];