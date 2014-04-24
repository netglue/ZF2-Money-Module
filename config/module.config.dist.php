<?php

return array(

	'ng_money' => array(

        /**
         * Seed the Currency List service with an array of allowed currencies.
         * This will limit what is available to select elements by default and what
         * is considered valid by the currency validator
         * @see \NetglueMoney\Service\CurrencyList
         * @see \NetglueMoney\Form\Element\SelectCurrency
         * @see \NetglueMoney\Validator\CurrencyCode
         * Also look at the factories for these
         */
        'allowCurrencies' => array(
		    'GBP', 'USD', 'EUR', 'JPY', 'CAD', 'AUD', 'NZD', 'HKD'
		),

        /**
         * If allowCurrencies is set, this option makes no sense, otherwise, it will
         * remove each code from those available by default in form elements and validators
         * In custom config, you can set 'allowCurrencies' to null
         */
		'excludeCurrencies' => array(
		    'ADB', 'CHE', 'CHW', 'MXV', 'USN', 'USS', 'UYI', 'XAG', 'XAU', 'XBA', 'XBB', 'XBC', 'XBD', 'XDR', 'XFU', 'XPD', 'XPT', 'XSU', 'XTS', 'XUA', 'XXX',
		),

	),

	/**
	 * Set a default locale if one isn't set system wide in php.ini so that locale dependent inputs
	 * can properly interpret numbers such as 1,000.00 or 1.100.000,00
	 */
	// 'locale' => 'de_DE',
);
