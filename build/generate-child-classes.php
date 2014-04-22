#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';

$template  = file_get_contents(__DIR__ . '/child-class.tpl');
$class     = new ReflectionClass('NetglueMoney\Money\Currency');
$attribute = $class->getProperty('currencies');
$attribute->setAccessible(true);

foreach (array_keys($attribute->getValue()) as $currencyCode) {
    if ($currencyCode == 'TRY') {
        continue;
    }

    file_put_contents(
        __DIR__ . '/../src/NetglueMoney/Money/Currency/' . $currencyCode . '.php',
        str_replace(
            '{currencyCode}',
            $currencyCode,
            $template
        )
    );
}
