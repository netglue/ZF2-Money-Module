<?php

namespace NetglueMoney\I18n;

use Locale;

use Zend\ServiceManager\ServiceManager;
use Zend\Form\FormElementManager;

class DefaultLocaleTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateInstance()
    {
        $default = new DefaultLocale;
        $this->assertInstanceOf('NetglueMoney\I18n\DefaultLocale', $default);
        $this->assertSame(Locale::getDefault(), $default->getLocale());

        return $default;
    }

    /**
     * @depends testCreateInstance
     */
    public function testSetGetLocale(DefaultLocale $default)
    {
        $locale = 'en_GB';
        $this->assertSame($default, $default->setLocale($locale));
        $this->assertSame($locale, $default->getLocale());
        $locale = 'en_US';
        $this->assertSame($default, $default->setLocale($locale));
        $this->assertSame($locale, $default->getLocale());

        return $default;
    }

    /**
     * @depends testSetGetLocale
     */
    public function testToString(DefaultLocale $default)
    {
        $locale = 'en_GB';
        $default->setLocale($locale);
        $this->assertSame($locale, $default->__toString());
        $locale = 'en_US';
        $default->setLocale($locale);
        $this->assertSame($locale, $default->__toString());

        return $default;
    }

    /**
     * @depends testToString
     */
    public function testSetLocaleFromConfig(DefaultLocale $default)
    {
        $sm = new ServiceManager;
        $sm->setAllowOverride(true);
        $sm->setService('config', array(
            'locale' => 'en_GB',
        ));

        $this->assertSame($default, $default->setLocaleFromConfig($sm));
        $this->assertSame('en_GB', $default->getLocale());

        $sm->setService('config', array(
            'locale' => 'en_US',
        ));
        $default->setLocaleFromConfig($sm);
        $this->assertSame('en_US', $default->getLocale());

        return $default;
    }

    public function testSetLocaleFromConfigWithServiceManagerSubclass()
    {
        $default = new DefaultLocale;
        $default->setLocale('en_US');

        $sm = new ServiceManager;
        $sm->setService('config', array(
            'locale' => 'uk_UA',
        ));
        $fm = new FormElementManager;
        $fm->setServiceLocator($sm);
        $default->setLocaleFromConfig($fm);

        $this->assertSame('uk_UA', $default->getLocale());

        return $default;
    }

    public function testCreateServiceReturnsSelfAndSetsDefaultLocale()
    {
        $default = new DefaultLocale;
        $sm = new ServiceManager;
        $sm->setService('config', array(
            'locale' => 'tl_PH',
        ));
        $result = $default->createService($sm);
        $this->assertSame($result, $default);

        $this->assertSame('tl_PH', $default->getLocale());

        return $default;
    }

    public function testIntialize()
    {
        $default = new DefaultLocale;
        $sm = new ServiceManager;
        $sm->setService('config', array(
            'locale' => 'sv_SE',
        ));

        $default->initialize('foo', $sm);
        $tester = new Tester;
        $this->assertSame(Locale::getDefault(), $tester->getLocale());
        $default->initialize($tester, $sm);
        $this->assertSame('sv_SE', $tester->getLocale());
    }

}

class Tester implements LocaleAwareInterface
{
    use LocaleAwareTrait;
}
