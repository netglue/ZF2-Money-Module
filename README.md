# ZF2 Money Module

A Zend Framework 2 module intended to make it easier to work with money.

[![Code Climate](https://codeclimate.com/github/netglue/ZF2-Money-Module/badges/gpa.svg)](https://codeclimate.com/github/netglue/ZF2-Money-Module) [![Test Coverage](https://codeclimate.com/github/netglue/ZF2-Money-Module/badges/coverage.svg)](https://codeclimate.com/github/netglue/ZF2-Money-Module)

Essentially, this module imports [Sebastian Bergmann's Money Library](https://github.com/sebastianbergmann/money) but probably controversially doesn't use it as a dependency with composer etc. The reason for this in the first instance was to get access to private static member vars in `Currency` but things have changed since first making this for ZF2 a long time ago. It's a useful module but really, it should be using [moneyphp/money](https://github.com/moneyphp/money) for its value objects. I have legacy code that uses the money VO's in the NetglueMoney namespace, so it's staying as-is for now but in future I'll probably migrate to moneyphp.

It's important to note that the Money value object stores the amount as an integer, the given currencies' smallest unit.

## Install

	$ composer require netglue/zf2-money-module

## Available Components

### Currency List

The currency list is intended to provide an easy way to configure the ISO currencies your app would be limited to using/accepting/whatever. A simple object that's retrievable form the service locator with `'NetglueMoney\Service\CurrencyList'`. Take a look in `module.config.dist.php` for more info on configuration.

### Currency Code Validator

Validates basic code format and also uses the currency list service to exclude codes that are not 'allowed' or unknown

### Currency Select Element

A select element that should be retrieved via the `FormElementManager` if you want it's validator and list of available currencies to all point to the same currency list.

Within your form element config, you should refer to the select element with its fully qualified class name `\NetglueMoney\Form\Element\SelectCurrency` - A `CurrencyCode` validator will automatically be added to it.

### Money Fieldset

A fieldset that will accept or hydrate to an instance of `NetglueMoney\Money\Money`. If you have a look at the tests you'll find an example of binding a model to a form that that hydrates the money instance.

### View Helpers

#### moneyFormat()

Largely based on `Zend\I18n\View\Helper\CurrencyFormat`, but the parameters have been changed:
	
	$money = new Money(123456, new Currency('GBP'));
	echo $this->moneyFormat($money); // £1,234.56 (Assuming default locale of en_GB)
	echo $this->moneyFormat($money, 'de_DE'); // 1.234,56 £
	echo $this->moneyFormat($money, 'de_DE', false); // 1.234 £
	
You can also specify custom format patterns - look at NumberFormatter docs for more info.

## Changelog
### 3.0.0
* Removed initializers and modernised to require PHP >= 7.1
* Compatible with Zend Framework 3
* Theoretically compatible with Expressive too


### 0.1.3
* Merged in changes from 1.5.0 of [sebastianbergmann/money](https://github.com/sebastianbergmann/money)

### 0.1.2
* Added `formMoney` view helper and tests for rendering text inputs that are intended to be used for inputting money. Accepts locale and uses a number formatter to format the input value to a locale string
* Added `setMoney()` and `getMoney()` methods to the money fieldset so it's easier to use when not binding an entity in a specific form and/or not being used with the form element manager


### 0.1.1
* Added `moneyFormat` view helper and tests

### 0.1.0
* Initial Release contains the Money and Currency value objects, ZF2 module setup along with a Money Fieldset and companion Hydrator and other stuff...


## Future Plans

* Migrate to using [moneyphp/money](https://github.com/moneyphp/money) for the value objects
