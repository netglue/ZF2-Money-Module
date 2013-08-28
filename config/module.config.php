<?php

return array(
	
	'ng_money' => array(
		'adapter' => array(
			'name' => 'openExchangeRates',
			'options' => array(
				'appId' => NULL,
				'accountType' => 'free',
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
	
	'router' => array(
		'routes' => array(
			'ng_money' => array(
				'type' => 'Literal',
				'options' => array(
					'route' => '/money',
					'defaults' => array(
						'__NAMESPACE__' => 'NetglueMoney\Controller',
						'controller' => 'Index',
						'action' => 'index',
					),
				),
			),
		),
	),
	
	'controllers' => array(
		'invokables' => array(
			'NetglueMoney\Controller\Index' => 'NetglueMoney\Controller\IndexController'
		),
	),
	'view_manager' => array(
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),
);
