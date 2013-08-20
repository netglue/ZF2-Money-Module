# Money/Forex Conversion Module

* Multiple different API implementations for retrieving rates
* Local storage/cache of historical rates
* Differentiate between EOD _(End of Day)_ and Average values for daily rates

* Money object _(Value Object)_ used to represent money values
* Money would use bcmath internally

```
$amount = (string) '1.34';
$code = 'GBP';
$date = DateTime::createFromFormat('j/n/Y G:i', '1/8/2013 12:00');
$money = new Money($amount, $code, $date);

$converted = $money->convertTo('ZAR');
```

