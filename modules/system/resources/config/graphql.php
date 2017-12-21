<?php

return [
	'schema'  => 'default',
	'schemas' => [
		'default' => [
			'mutation' => [
				'config' => \System\Conf\Graphql\Mutation\ConfigMutation::class,
			],
			'query'    => [
				'config' => \System\Conf\Graphql\Queries\ConfigQuery::class,
			],
		],
	],
	'types'   => [
		/* input
		 -------------------------------------------- */
		// config
		\System\Conf\GraphQL\Input\ConfigItemType::class,


		/* query
		 -------------------------------------------- */
		// config
		\System\Conf\GraphQL\Types\ConfItemType::class,
		
		// resp
		\System\Conf\GraphQL\Types\RespType::class,

	],
];