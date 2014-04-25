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

    /**
     * Set the current default locale by finding it in config
     * @param ServiceLocatorInterface $serviceLocator
     * @return self
     */
    public function setLocaleFromConfig(ServiceLocatorInterface $serviceLocator)
    {
        /**
         * We could be getting a Form Element Manager, Validator Manager et al
         */
        if(is_subclass_of($serviceLocator, 'Zend\ServiceManager\ServiceManager')) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }
        $config = $serviceLocator->get('config');
        $locale = isset($config['locale']) ? $config['locale'] : Locale::getDefault();
        $this->setLocale($locale);
        return $this;
    }

    /**
     * Implements intializer interface
     * @param mixed $instance
     * @param ServiceLocatorInterface $serviceLocator
     * @return void
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        /**
         * We might not have an object and without this, when the config
         * service is created (An array most likely), we will end up in an infinite loop
         * as setLocaleFromConfig() requires the config service
         */
        if($instance === $this || !is_object($instance)) {
            return;
        }

        /**
         * Use this as an opportunity to set myself up
         */
        if(null === $this->locale) {
            $this->setLocaleFromConfig($serviceLocator);
        }

        /**
         * Inject based on interface
         */
        if($instance instanceof LocaleAwareInterface) {
            $instance->setLocale($this->getLocale());
        }
    }

    /**
     * Set the default locale
     * @param string $locale
     * @return self
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Return the current default locale or return Locale::getDefault() if unset
     * @return string
     */
    public function getLocale()
    {
        if(null === $this->locale) {
            return Locale::getDefault();
        }
        return $this->locale;
    }

    /**
     * To string calls getLocale()
     * @return string
     */
    public function __toString()
    {
        return $this->getLocale();
    }
}
