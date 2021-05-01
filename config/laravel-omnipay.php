<?php

return [

	// The default gateway to use
	'default' => 'freekassa',

	// Add in each gateway here
	'gateways' => [
		'freekassa' => [
			'driver'  => 'FreeKassa',
			'options' => [
				'purse'   => '',
				'secretKey'    => '',
				'secretKey2' => ''
			]
		]
	]

];