<?php

return [
	'schema'  => 'default',
	'schemas' => [
		'default' => [
			'mutation' => [
				\System\Setting\Graphql\Mutation\SettingMutation::class,
			],
			'query'    => [
				\System\Setting\Graphql\Queries\SettingQuery::class,
				\System\Setting\Graphql\Queries\SettingsQuery::class,
			],
		],
	],
	'types'   => [
		/* input
		 -------------------------------------------- */
		// config
		\System\Setting\GraphQL\Input\ConfigItemType::class,


		/* query
		 -------------------------------------------- */
		// config
		\System\Setting\GraphQL\Types\SettingType::class,

		// resp
		\System\Setting\GraphQL\Types\RespType::class,

	],
];