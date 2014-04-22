<?php

return array(

	'ng_money' => array(
		'adapter' => array(
			'name' => 'openExchangeRates',
			'options' => array(
				//'appId' => NULL,
				//'accountType' => 'free',
			),
		),
		'cache' => array(
			'adapter' => array(
				'name' => 'filesystem',
				'options' => array(
					'ttl' => 7200,
				),
			),
		),
		'converter' => array(
			'round' => true,
			'precision' => 2,
			'roundMode' => PHP_ROUND_HALF_UP,
		),
	),

	'service_manager' => array(
		'factories' => array(
			'CurrencyConverter' => 'NetglueMoney\Service\CurrencyConverterFactory',
		),
	),


	'view_manager' => array(
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),
);
