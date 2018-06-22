<?php
declare(strict_types=1);

namespace NetglueMoneyTest\View\Helper;

use NetglueMoney\Money\Money;
use NetglueMoney\Money\Currency;
use NetglueMoneyTest\Framework\TestCase;
use NetglueMoney\View\Helper\MoneyFormat;

class MoneyFormatTest extends TestCase
{

    public function setUp()
    {
        ini_set('intl.default_locale', 'en_US');
        setlocale(LC_ALL, 'en_US');
    }

    public function testSetGetLocale()
    {
        $helper = new MoneyFormat;
        $this->assertSame($helper, $helper->setLocale('en_GB'));
        $this->assertSame('en_GB', $helper->getLocale());
    }

    public function testGetLocaleReturnsDefault()
    {
        $helper = new MoneyFormat;
        $this->assertSame(\Locale::getDefault(), $helper->getLocale());
    }

    public function testShouldShowDecimalsBasic()
    {
        $helper = new MoneyFormat;
        $this->assertInternalType(
            'bool',
            $helper->shouldShowDecimals(),
            'Decimal flag should initially be set to a bool'
        );
        $this->assertSame($helper, $helper->setShouldShowDecimals(false));
        $this->assertFalse($helper->shouldShowDecimals());
        $helper->setShouldShowDecimals(true);
        $this->assertTrue($helper->shouldShowDecimals());
    }

    public function testSetGetCurrencyPattern()
    {
        $helper = new MoneyFormat;
        $this->assertNull($helper->getCurrencyPattern());
        $this->assertSame($helper, $helper->setCurrencyPattern('Foo'));
        $this->assertSame('Foo', $helper->getCurrencyPattern());
    }

    public function testFormatting()
    {
        $helper = new MoneyFormat;
        $money = new Money(100000, new Currency('GBP'));
        $helper->setLocale('en_GB');
        $this->assertEquals('£1,000.00', $helper($money));
        $this->assertEquals('GBP 1,0,0,0.00', $helper($money, 'en_GB', true, '¤¤ #,#,0.##'));
        $this->assertEquals('GBP 1,0,0,0', $helper($money, 'en_GB', false, '¤¤ #,#,0.##'));
        $this->assertEquals('1.000,00 £', $helper($money, 'de-DE'));
    }
}
