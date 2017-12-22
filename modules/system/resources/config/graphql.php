<?php

return [
	'schema'  => 'default',
	'schemas' => [
		'default' => [
			'mutation' => [
				'config' => \System\Setting\Graphql\Mutation\ConfigMutation::class,
			],
			'query'    => [
				'config' => \System\Setting\Graphql\Queries\ConfigQuery::class,
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
		\System\Setting\GraphQL\Types\ConfItemType::class,
		
		// resp
		\System\Setting\GraphQL\Types\RespType::class,

	],
];