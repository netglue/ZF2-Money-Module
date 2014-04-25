<?php

namespace NetglueMoney\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\FormElementManager;

use NetglueMoney\Form\Element\SelectCurrency;

class CurrencySelectFactory implements FactoryInterface
{

    /**
     * Return a Currency Select Instance
     * @return SelectCurrency
     */
    public function createService(ServiceLocatorInterface $formElementManager)
    {
        if (!$formElementManager instanceof FormElementManager) {
            throw new \UnexpectedValueException('Expected an instance of the Form Element Manager. Received '.get_class($formElementManager));
        }
        $appServices = $formElementManager->getServiceLocator();

        $list = $appServices->get('NetglueMoney\Service\CurrencyList');

        $select = new SelectCurrency;
        $select->setCurrencyList($list);

        $validatorManager = $appServices->get('ValidatorManager');
        $select->setValidator($validatorManager->get('NetglueMoney\Validator\CurrencyCode'));

        return $select;
  }

}
