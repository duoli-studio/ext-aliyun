<?php

return [
	'schema'                => 'default',
	'schemas'               => [
		'default' => [
			'mutation' => [],
			'query'    => [
				\System\Setting\Graphql\Queries\SettingsQuery::class,
			],
		],
		'web' => [
			'mutation' => [],
			'query'    => [
				\System\Setting\Graphql\Queries\SettingsQuery::class,
			],
		],
		'backend' => [
			'mutation' => [
				\System\Setting\Graphql\Mutation\SettingMutation::class,
				\System\Pam\GraphQL\Mutation\RoleMutation::class,

				/* server
				 -------------------------------------------- */
				\Order\Game\GraphQL\Mutation\ServerMutation::class,
				\Order\Game\GraphQL\Mutation\ServerDeleteMutation::class,
			],
			'query'    => [
				\System\Setting\Graphql\Queries\SettingQuery::class,
				\System\Setting\Graphql\Queries\SettingsQuery::class,

				\Order\game\Graphql\Queries\ServersQuery::class,
				\Order\game\Graphql\Queries\ServerQuery::class,

				/* role
				 -------------------------------------------- */
				\System\Pam\Graphql\Queries\RolesQuery::class,
				\System\Pam\Graphql\Queries\RoleQuery::class,
			],
		],
	],
	'middleware_schema'     => [
		'default' => ['cross'],
		'backend' => ['auth:jwt_backend', 'cross'],
		'web'     => ['auth:jwt_web', 'cross'],
	],
	'json_encoding_options' => JSON_UNESCAPED_UNICODE,
	'types'                 => [
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