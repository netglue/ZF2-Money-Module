<?php

namespace NetglueMoney\Form\Element;

use Zend\Form\Element\Text as TextElement;
use Zend\InputFilter\InputProviderInterface;
use NetglueMoney\Money\Money;
use NetglueMoney\Money\Currency;
use NumberFormatter;
use Locale;

class MoneyValue extends TextElement implements InputProviderInterface
{

    protected $formatter;

    public function setLocale($locale = null)
    {
        return $this->setOption('locale', $locale);
    }

    public function getLocale()
    {
        $locale = $this->getOption('locale');
        if(empty($locale)) {
            $locale = Locale::getDefault();
        }
        return $locale;
    }

    public function getFormatter()
    {
        if(!$this->formatter) {
            $formatter = NumberFormatter::create($this->getLocale(), NumberFormatter::CURRENCY);
        }
        return $formatter;
    }

    public function setValue($value)
    {
        $float = $this->getFormatter()->parseCurrency($value, $code);
        if(false === $float) {
            // ??
        }
        $currency = new Currency($code);
        $subUnit = $currency->getSubUnit();
        $int = (int) ($float * $subUnit);
        $money = new Money($int, $currency);
        return parent::setValue($money);
    }

    /**
     * Return input filter spec
     * @return array
     */
    public function getInputSpecification()
    {
        return array(
            'name' => $this->getName(),
            'required' => true,
            'filters' => array(
                array('name' => 'Zend\Filter\StringTrim'),
                //array('name' => 'Zend\Filter\StringToUpper'),
            ),
            'validators' => array(
                //$this->getValidator(),
            ),
        );
    }
}
