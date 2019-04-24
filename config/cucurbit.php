<?php

return [
	/* menus setting for modules manager
	 * ---------------------------------------- */
	'menus'    => [

	],


	/* database setting
	 * ---------------------------------------- */
	'database' => [
		'driver'      => 'mysql',
		'connections' => [
			'mysql' => [
				'driver'    => 'mysql',
				'host'      => '127.0.0.1',
				'port'      => '3306',
				'database'  => 'database',
				'username'  => 'username',
				'password'  => 'password',
				'charset'   => 'utf8mb4',
				'collation' => 'utf8mb4_unicode_ci',
				'prefix'    => '',
			],
		],
	],
];