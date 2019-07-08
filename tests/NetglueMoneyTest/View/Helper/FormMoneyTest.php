<?php
declare(strict_types=1);

namespace NetglueMoneyTest\View\Helper;

use Locale;
use NetglueMoney\View\Helper\FormMoney;
use NetglueMoneyTest\Framework\TestCase;
use NumberFormatter;
use Zend\Form\Element;

class FormMoneyTest extends TestCase
{

    protected function setUp() : void
    {
        ini_set('intl.default_locale', 'en_US');
        setlocale(LC_ALL, 'en_US');
    }

    public function testGetLocaleReturnsDefault() : void
    {
        $helper = new FormMoney();
        $default = Locale::getDefault();
        $this->assertSame($default, $helper->getLocale());
    }

    public function testSetGetLocale() : void
    {
        $helper = new FormMoney();
        $helper->setLocale('en_GB');
        $this->assertSame('en_GB', $helper->getLocale());

        $helper->setLocale('en_US');
        $this->assertSame('en_US', $helper->getLocale());
    }

    public function testCanNullifyLocale() : void
    {
        $helper = new FormMoney();
        $default = Locale::getDefault();

        $helper->setLocale('test');
        $helper->setLocale(null);
        $this->assertSame($default, $helper->getLocale());
    }

    public function testGetFormatter() : void
    {
        $helper = new FormMoney;
        $gbFormatter = $helper->getFormatter('en_GB');
        $this->assertSame($gbFormatter, $helper->getFormatter('en_GB'));

        $this->assertSame(
            'en_GB',
            $gbFormatter->getLocale(Locale::VALID_LOCALE),
            'Expected number formatter to have the locale en_GB'
        );

        $fr = $helper->getFormatter('fr-FR');
        $this->assertSame('fr_FR', $fr->getLocale(Locale::VALID_LOCALE));
        $this->assertStringStartsWith('fr', $fr->getLocale(Locale::ACTUAL_LOCALE));
    }

    public function testGetFormatterReturnsFormatterWithDefaultLocale() : void
    {
        $helper = new FormMoney;
        $formatter = $helper->getFormatter();
        $this->assertInstanceOf('NumberFormatter', $formatter);
        $this->assertSame($helper->getLocale(), $formatter->getLocale(Locale::VALID_LOCALE));

        $helper->setLocale('de_DE');
        $formatter = $helper->getFormatter();
        $this->assertInstanceOf('NumberFormatter', $formatter);
        $this->assertStringStartsWith('de', $formatter->getLocale());
    }

    public function testFormatValue() : void
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
    public function testRaisesExceptionWhenNameIsNotPresentInElement() : void
    {
        $helper = new FormMoney;
        $element = new Element();
        $helper->render($element);
    }

    public function testRenderHasFormattedValueAttribute() : void
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
