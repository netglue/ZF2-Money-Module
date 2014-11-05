<?php

namespace NetglueMoney\View\Helper;
use Locale;
use NumberFormatter;
use Zend\Form\Element;
class FormMoneyTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        setlocale(LC_ALL, 'en_US');
    }

    public function testCanInstantiate()
    {
        $helper = new FormMoney;
        return $helper;
    }

    /**
     * @depends testCanInstantiate
     */
    public function testGetLocaleReturnsDefault(FormMoney $helper)
    {
        $default = Locale::getDefault();
        $this->assertSame($default, $helper->getLocale());
    }

    /**
     * @depends testCanInstantiate
     */
    public function testSetGetLocale(FormMoney $helper)
    {
        $helper->setLocale('en_GB');
        $this->assertSame('en_GB', $helper->getLocale());

        $helper->setLocale('en_US');
        $this->assertSame('en_US', $helper->getLocale());
    }

    /**
     * @depends testCanInstantiate
     */
    public function testCanNullifyLocale(FormMoney $helper)
    {
        $default = Locale::getDefault();

        $helper->setLocale('test');
        $helper->setLocale(null);
        $this->assertSame($default, $helper->getLocale());
    }

    public function testGetFormatter()
    {
        $helper = new FormMoney;
        $gb = $helper->getFormatter('en_GB');
        $this->assertInstanceOf('NumberFormatter', $gb);
        $this->assertSame($gb, $helper->getFormatter('en_GB'));

        $this->assertSame('en_GB', $gb->getLocale(), 'Expected number formatter to have the locale en_GB');

        $fr = $helper->getFormatter('fr-FR');
        $this->assertSame('fr_FR', $fr->getLocale(Locale::VALID_LOCALE));
        $this->assertSame('fr', $fr->getLocale(Locale::ACTUAL_LOCALE));
    }

    public function testGetFormatterReturnsFormatterWithDefaultLocale()
    {
        $helper = new FormMoney;
        $formatter = $helper->getFormatter();
        $this->assertInstanceOf('NumberFormatter', $formatter);
        $this->assertSame($helper->getLocale(), $formatter->getLocale());

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
     * @depends testCanInstantiate
     */
    public function testRaisesExceptionWhenNameIsNotPresentInElement(FormMoney $helper)
    {
        $element = new Element();
        $this->setExpectedException('Zend\Form\Exception\DomainException', 'name');
        $helper->render($element);
    }

    /**
     * @depends testCanInstantiate
     */
    public function testRenderHasFormattedValueAttribute(FormMoney $helper)
    {
        $element = new Element('foo');
        $element->setValue(1234.56);
        $helper->setLocale('de_DE');
        $markup = $helper->render($element);
        $expect = 'value="1.234,56"';
        $pos = strpos($markup, $expect);
        $this->assertGreaterThan(1, $pos);
    }

}
