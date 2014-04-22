<?php

namespace NetglueMoney\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use NetglueMoney\Service\CurrencyList;

class CurrencyListFactory implements FactoryInterface {

	/**
	 * Return Currency List Instance
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return CurrencyList
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
        $config = $serviceLocator->get('config');
        $config = isset($config['ng_money']) ? $config['ng_money'] : array();
        $list = new CurrencyList;

        if(isset($config['allowCurrencies']) && is_array($config['allowCurrencies'])) {
            $list->setAllow($config['allowCurrencies']);
        }
        if(isset($config['excludeCurrencies'])  && is_array($config['excludeCurrencies'])) {
            $list->remove($config['excludeCurrencies']);
        }

        return $list;
    }

}
