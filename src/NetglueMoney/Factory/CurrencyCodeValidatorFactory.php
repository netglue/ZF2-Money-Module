<?php

namespace NetglueMoney\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

//use Zend\Form\FormElementManager;

use NetglueMoney\Validator\CurrencyCode;

class CurrencyCodeValidatorFactory implements FactoryInterface {

	/**
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return
	 */
	public function createService(ServiceLocatorInterface $validatorManager)
	{
        $serviceLocator = $validatorManager->getServiceLocator();
        $list = $serviceLocator->get('NetglueMoney\Service\CurrencyList');
        $validator = new CurrencyCode;
        $validator->setCurrencyList($list);

        return $validator;
    }

}
