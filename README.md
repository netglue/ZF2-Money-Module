# ZF2 Money Module

A Zend Framework 2 module intended to make it easier to work with money.

Essentially, this module imports [Sebastian Bergmann's Money Library](https://github.com/sebastianbergmann/money) but probably controversially doesn't use it as a dependency with composer etc. The reason for this in the first instance was to get access to private static member vars in `Currency` but in future I will likely want to modify the basic value objects more.

It's important to note that the Money value object stores the amount as an integer, the given currencies' smallest unit.

## Install

	{
		"require": {
			"netglue/zf2-money-module": "0.1.*"
		}
	}

## Available Components

### Currency List

The currency list is intended to provide an easy way to configure the ISO currencies your app would be limited to using/accepting/whatever. A simple object that's retrievable form the service locator with `'NetglueMoney\Service\CurrencyList'`. Take a look in `module.config.dist.php` for more info on configuration.

There's a handy intializer you can bung into any of your service configurations under the initializers key for form elements, validators etc. Just add the class name to your module method or config file.

	public function getFormElementConfig() {
		return array(
			'initializers' => array(
				'NetglueMoney\Factory\CurrencyListInitializer',
			),
		);
	}

The initializer is not registered by default anywhere. All the instances in this module that use it have factories that supply it manually.

### Currency Select Element

A select element that should be retrieved via the `FormElementManager` if you want it's validator and list of available currencies to all point to the same currency list.

Within your form elememt config, you should be able to specify `'type' => 'SelectCurrency'` - A `CurrencyCode` validator will automatically be added to it.

### Money Fieldset

A fieldset that will accept or hydrate to an instance of `NetglueMoney\Money\Money`. If you have a look at the tests you'll find an example of binding a model to a form that that hydrates the money instance.

### Default Locale Initializer

As the fieldset should be able to convert arbitrary localized numbers to something useful, i.e. in UK, 1,234.56 is 1234.56 and in DE, 1.234,56 - there's a locale initializer that provides either a configured default locale or the one found in `Locale::getDefault()`



## Changelog

### 0.1.0
* Initial Release contains the Money and Currency value objects, ZF2 module setup along with a Money Fieldset and companion Hydrator and other stuff...
