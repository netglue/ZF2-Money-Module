<?php
declare(strict_types=1);

namespace NetglueMoneyTest\View\Helper;

use Locale;
use NetglueMoney\View\Helper\FormMoney;
use NumberFormatter;
use Zend\Form\Element;
use NetglueMoneyTest\Framework\TestCase;

class FormMoneyTest extends TestCase
{

    public function setUp()
    {
        ini_set('intl.default_locale', 'en_US');
        setlocale(LC_ALL, 'en_US');
    }

    public function testGetLocaleReturnsDefault()
    {
        $helper = new FormMoney();
        $default = Locale::getDefault();
        $this->assertSame($default, $helper->getLocale());
    }

    public function testSetGetLocale()
    {
        $helper = new FormMoney();
        $helper->setLocale('en_GB');
        $this->assertSame('en_GB', $helper->getLocale());

        $helper->setLocale('en_US');
        $this->assertSame('en_US', $helper->getLocale());
    }

    public function testCanNullifyLocale()
    {
        $helper = new FormMoney();
        $default = Locale::getDefault();

        $helper->setLocale('test');
        $helper->setLocale(null);
        $this->assertSame($default, $helper->getLocale());
    }

    public function testGetFormatter()
    {
        $helper = new FormMoney;
        $gbFormatter = $helper->getFormatter('en_GB');
        $this->assertInstanceOf(NumberFormatter::class, $gbFormatter);
        $this->assertSame($gbFormatter, $helper->getFormatter('en_GB'));

        $this->assertSame(
            'en_GB',
            $gbFormatter->getLocale(Locale::VALID_LOCALE),
            'Expected number formatter to have the locale en_GB'
        );

        $fr = $helper->getFormatter('fr-FR');
        $this->assertSame('fr_FR', $fr->getLocale(Locale::VALID_LOCALE));
        $this->assertSame('fr', $fr->getLocale(Locale::ACTUAL_LOCALE));
    }

    public function testGetFormatterReturnsFormatterWithDefaultLocale()
    {
        $helper = new FormMoney;
        $formatter = $helper->getFormatter();
        $this->assertInstanceOf('NumberFormatter', $formatter);
        $this->assertSame($helper->getLocale(Locale::VALID_LOCALE), $formatter->getLocale(Locale::VALID_LOCALE));

        $helper->setLocale('de_DE');
        $formatter = $helper->getFormatter();
        $this->assertInstanceOf('NumberFormatter', $formatter);
        $this->assertSame('de', $formatter->getLocale());
    }

    public function testFormatValue()
    {
        $helper = new FormMoney;
        $helper->setLocale('de_DE');
        $this->assertSame('123.456.789,12', $helper->formatValue(123456789.12));

        $helper->setLocale('en_GB');
        $this->assertSame('123,456,789.12', $helper->formatValue(123456789.12));
    }

    /**
     * @expectedException \Zend\Form\Exception\DomainException
     */
    public function testRaisesExceptionWhenNameIsNotPresentInElement()
    {
        $helper = new FormMoney;
        $element = new Element();
        $helper->render($element);
    }

    public function testRenderHasFormattedValueAttribute()
    {
        $helper = new FormMoney;
        $element = new Element('foo');
        $element->setValue(1234.56);
        $helper->setLocale('de_DE');
        $markup = $helper->render($element);
        $expect = 'value="1.234,56"';
        $pos = strpos($markup, $expect);
        $this->assertGreaterThan(1, $pos);
    }
}
