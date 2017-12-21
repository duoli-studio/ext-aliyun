<?php
/**
 * 请参考
 * http://doc3.workerman.net/gateway-worker-development/store-config.html
 * http://doc3.workerman.net/appendices/mysql.html
 */
return [
	'credential_processor' => 'zgldh\workerboy\CredentialProcessor', //凭证处理器，用于同步web服务器和socket服务器的用户session

	'applications' => [        // 请仿照下面将自己建立的Workerman3应用的start.php文件路径填进去
		'app/Handlers/Workerman/Abc/start.php' => [
			'store' => [
				'driver'    => 'file',
				// 'file', 'memcache'
				'gateway'   => [       // 仅对 driver == 'memcache' 启用
					'127.0.0.1:11211',
				],
				'room'      => [      // 仅对 driver == 'memcache' 启用
					'127.0.0.1:11211',
				],
				'storePath' => sys_get_temp_dir() . '/workerman-demo/' //每一个application的目录都应该不一样
				// 仅对 driver == 'file' 启用. 默认放在系统临时目录。
			],
			'db'    => [    // 不推荐使用Workerman3内置数据库连接。请使用Laravel的Eloquent
				'db1' => [
					'host'     => '127.0.0.1',
					'port'     => 3306,
					'user'     => 'mysql_user',
					'password' => 'mysql_password',
					'dbname'   => 'db1',
					'charset'  => 'utf8',
				],
			],
		]
		//        ,'app/WorkermanApps/Chat/start.php'=>array(...) 这是第二个应用，请仿照仿照上面填写。但具体地址、路径一定不要完全一样。否则会混乱
	],
];