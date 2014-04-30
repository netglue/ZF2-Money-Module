<?php

namespace NetglueMoney\View\Helper;

use NetglueMoney\I18n\LocaleAwareInterface;
use Zend\I18n\View\Helper\CurrencyFormat;

class MoneyFormatTest extends \PHPUnit_Framework_TestCase
{

    public function testCanInstantiate()
    {
        $helper = new MoneyFormat;
        return $helper;
    }



}
