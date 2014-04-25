<?php

namespace NetglueMoney\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\InitializerInterface;

use NetglueMoney\Service\CurrencyListAwareInterface;

class CurrencyListInitializer implements InitializerInterface
{

    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if (is_object($instance) && $instance instanceof CurrencyListAwareInterface) {
            $instance->setCurrencyList($this->getCurrencyList($serviceLocator));
        }
    }

    public function getCurrencyList(ServiceLocatorInterface $serviceLocator)
    {
        if (is_subclass_of($serviceLocator, 'Zend\ServiceManager\ServiceManager')) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        return $serviceLocator->get('NetglueMoney\Service\CurrencyList');
    }

}
