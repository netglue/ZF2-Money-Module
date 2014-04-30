<?php

namespace NetglueMoney\View\Helper;
use NetglueMoney\Money\Money;
use NetglueMoney\Money\Currency;
use Locale;
use NumberFormatter;

class MoneyFormatTest extends \PHPUnit_Framework_TestCase
{

    public function testCanInstantiate()
    {
        $helper = new MoneyFormat;
        return $helper;
    }

    /**
     * @depends testCanInstantiate
     */
    public function testSetGetLocale(MoneyFormat $helper)
    {
        $this->assertSame($helper, $helper->setLocale('en_GB'));
        $this->assertSame('en_GB', $helper->getLocale());

        return $helper;
    }

    public function testGetLocaleReturnsDefault()
    {
        $helper = new MoneyFormat;
        $this->assertSame(\Locale::getDefault(), $helper->getLocale());
    }

    /**
     * @depends testSetGetLocale
     */
    public function testShouldShowDecimalsBasic(MoneyFormat $helper)
    {
        $this->assertInternalType('bool', $helper->shouldShowDecimals(), 'Decimal flag should initially be set to a bool');
        $this->assertSame($helper, $helper->setShouldShowDecimals(false));
        $this->assertFalse($helper->shouldShowDecimals());
        $helper->setShouldShowDecimals(1);
        $this->assertTrue($helper->shouldShowDecimals());
        return $helper;
    }

    /**
     * @depends testShouldShowDecimalsBasic
     */
    public function testSetGetCurrencyPattern(MoneyFormat $helper)
    {
        $this->assertNull($helper->getCurrencyPattern());
        $this->assertSame($helper, $helper->setCurrencyPattern('Foo'));
        $this->assertSame('Foo', $helper->getCurrencyPattern());
        $helper->setCurrencyPattern(NULL);
        $this->assertNull($helper->getCurrencyPattern());
        return $helper;
    }

    /**
     * @depends testSetGetCurrencyPattern
     */
    public function testGetFormatter(MoneyFormat $helper)
    {
        $gb = $helper->getFormatter('en-GB');
        $this->assertInstanceOf('NumberFormatter', $gb);
        $this->assertSame($gb, $helper->getFormatter('en-GB'));

        $this->assertSame('en_GB', $gb->getLocale());

        $fr = $helper->getFormatter('fr-FR');
        $this->assertSame('fr_FR', $fr->getLocale(Locale::VALID_LOCALE));
        $this->assertSame('fr', $fr->getLocale(Locale::ACTUAL_LOCALE));
    }

    public function testGetFormatterReturnsFormatterWithDefaultLocale()
    {
        $helper = new MoneyFormat;
        $formatter = $helper->getFormatter();
        $this->assertInstanceOf('NumberFormatter', $formatter);
        $this->assertSame($helper->getLocale(), $formatter->getLocale());

        $helper->setLocale('de_DE');
        $formatter = $helper->getFormatter();
        $this->assertInstanceOf('NumberFormatter', $formatter);
        $this->assertSame('de', $formatter->getLocale());

    }

    /**
     * @depends testSetGetCurrencyPattern
     */
    public function  testFormatting(MoneyFormat $helper)
    {
        $money = new Money(100000, new Currency('GBP'));
        $helper->setLocale('en_GB');
        $this->assertEquals('£1,000.00', $helper($money));
        $this->assertEquals('GBP 1,0,0,0.00', $helper($money, 'en_GB', true, '¤¤ #,#,0.##'));
        $this->assertEquals('GBP 1,0,0,0', $helper($money, 'en_GB', false, '¤¤ #,#,0.##'));
        $this->assertEquals('1.000,00 £', $helper($money, 'de-DE'));

        return $helper;
    }

}
