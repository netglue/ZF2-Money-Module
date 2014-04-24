<?php

namespace NetglueMoney\I18n;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\InitializerInterface;

use Locale;
use NetglueMoney\Exception;

class DefaultLocale implements
    FactoryInterface,
    InitializerInterface
{

    protected $locale;

    /**
	 * Return an instance of myself
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return DefaultLocale
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
        if(null === $this->locale) {
            $this->setLocaleFromConfig($serviceLocator);
        }

        return $this;
    }

    public function setLocaleFromConfig(ServiceLocatorInterface $serviceLocator)
    {
        if(is_subclass_of($serviceLocator, 'Zend\ServiceManager\ServiceManager')) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }
        $config = $serviceLocator->get('config');
        $locale = isset($config['locale']) ? $config['locale'] : Locale::getDefault();
        $this->setLocale($locale);
        return $this;
    }

    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if($instance === $this || !is_object($instance)) {
            return;
        }
        if(null === $this->locale) {
            $this->setLocaleFromConfig($serviceLocator);
        }
        if($instance instanceof LocaleAwareInterface) {
            $instance->setLocale($this->getLocale());
        }
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    public function getLocale()
    {
        if(null === $this->locale) {
            $this->locale = Locale::getDefault();
        }
        return $this->locale;
    }

    public function __toString()
    {
        return $this->getLocale();
    }
}
