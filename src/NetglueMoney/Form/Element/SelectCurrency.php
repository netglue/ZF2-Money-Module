<?php

namespace NetglueMoney\Form\Element;

use Zend\Form\Element\Select;
use NetglueMoney\Money\Currency;

class SelectCurrency extends Select
{

    /**
     * Init - Adds Options
     * @return void
     */
    public function init()
    {
        $codes = Currency::getAvailableCurrencyNames();
        $options = array();
        foreach($codes as $code => $name) {
            $options[$code] = $name;
        }
        sort($options);
        $this->setValueOptions($options);
    }

}
