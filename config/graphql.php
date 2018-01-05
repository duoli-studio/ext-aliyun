<?php

return [
	'schema'                => 'default',
	'schemas'               => [
		'default' => [
			'mutation' => [],
			'query'    => [
				\Util\Util\GraphQL\Queries\SendCaptchaQuery::class,
			],
		],
		'web'     => [
			'mutation' => [
				\User\Fans\GraphQL\Mutation\FansMutation::class,
				\User\Fans\GraphQL\Mutation\FansDeleteMutation::class,

				\User\Pam\GraphQL\Mutation\UnbindMutation::class,
			],
			'query'    => [
				\System\Setting\Graphql\Queries\SettingsQuery::class,

				\User\Fans\Graphql\Queries\ConcernQuery::class,
				\Util\Util\GraphQL\Queries\SendCaptchaQuery::class,
			],
		],
		'backend' => [
			'mutation' => [
				\System\Setting\Graphql\Mutation\SettingMutation::class,
				\System\Pam\GraphQL\Mutation\RoleMutation::class,

				\System\Pam\GraphQL\Mutation\BindChangeMutation::class,
				\User\Pam\GraphQL\Mutation\ChangeMutation::class,

				/* server
				 -------------------------------------------- */
				\Order\Game\GraphQL\Mutation\ServerMutation::class,
				\Order\Game\GraphQL\Mutation\ServerDeleteMutation::class,
				\User\Pam\GraphQL\Mutation\PwdRegisterMutation::class,
				\User\Pam\GraphQL\Mutation\CaptchaRegisterMutation::class
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



				\System\Pam\Graphql\Queries\BindChangeQuery::class,
				\User\Pam\Graphql\Queries\ChangeQuery::class,
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

		\System\Pam\GraphQL\Types\BindChangeType::class,

		/* server
		 -------------------------------------------- */
		\Order\Game\GraphQL\Types\ServerType::class,


		\User\Pam\GraphQL\Types\ChangeType::class,


		/* util
		 -------------------------------------------- */
		// send captcha
		\Util\Util\GraphQL\Types\SendCaptchaTypeType::class,


		/* user
		 -------------------------------------------- */
		// concern
		\User\Fans\GraphQL\Types\ConcernType::class,
		\User\Pam\GraphQL\Types\PwdRegisterType::class,
		\User\Pam\GraphQL\Types\CaptchaRegisterType::class

	],
];