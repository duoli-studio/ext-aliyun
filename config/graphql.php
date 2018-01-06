<?php

return [
	'schema'                => 'default',
	'schemas'               => [
		// 无权限即可访问
		'default' => [
			'mutation' => [
				\User\Pam\GraphQL\Mutation\PasswordLoginMutation::class,
				\Util\Util\GraphQL\Mutation\SendCaptchaMutation::class,
				\User\Pam\GraphQL\Mutation\CaptchaLoginMutation::class,
				\User\Pam\GraphQL\Mutation\UpdatePasswordMutation::class
			],
			'query'    => [
			],
		],
		// 前台用户权限
		'web'     => [
			'mutation' => [
				\User\Fans\GraphQL\Mutation\FansMutation::class,
				\User\Fans\GraphQL\Mutation\FansDeleteMutation::class,

				\User\Pam\GraphQL\Mutation\UnbindMutation::class,
				\User\Pam\GraphQL\Mutation\ProfileMutation::class,

				\System\Pam\GraphQL\Mutation\BindChangeMutation::class,
				\Util\Util\GraphQL\Mutation\SendCaptchaMutation::class,

			],


			'query'    => [
				\System\Setting\Graphql\Queries\SettingsQuery::class,

				\User\Fans\Graphql\Queries\ConcernQuery::class,
				\User\Fans\Graphql\Queries\FansQuery::class,

				\Order\Game\Graphql\Queries\UserServerQuery::class,
			],
		],
		// 后台权限
		'backend' => [
			'mutation' => [
				\System\Setting\Graphql\Mutation\SettingMutation::class,
				\System\Pam\GraphQL\Mutation\RoleMutation::class,

				\System\Pam\GraphQL\Mutation\BindChangeMutation::class,


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

		\System\Pam\GraphQL\Types\BindChangeType::class,

		/* server
		 -------------------------------------------- */
		\Order\Game\GraphQL\Types\ServerType::class,
		\Order\Game\GraphQL\Types\UserServerType::class,


		\User\Pam\GraphQL\Types\ProfileChangeType::class,


		/* util
		 -------------------------------------------- */
		// send captcha
		\Util\Util\GraphQL\Types\SendCaptchaType::class,


		/* user
		 -------------------------------------------- */
		// concern
		\User\Fans\GraphQL\Types\ListType::class,
		\User\Pam\GraphQL\Types\PwdRegisterType::class,
		\User\Pam\GraphQL\Types\CaptchaRegisterType::class,

	],
];