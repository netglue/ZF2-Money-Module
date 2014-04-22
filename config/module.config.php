<?php

return array(

	'ng_money' => array(

		'allowCurrencies' => NULL,
		'excludeCurrencies' => NULL,

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

);
