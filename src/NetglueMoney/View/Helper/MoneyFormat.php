<?php
namespace NetglueMoney\View\Helper;

use NetglueMoney\I18n\LocaleAwareInterface;
use Zend\I18n\View\Helper\CurrencyFormat;
use NetglueMoney\Money\Money;
use NetglueMoney\Money\Currency;

class MoneyFormat extends CurrencyFormat implements LocaleAwareInterface
{

    /**
     * Invoke - accepts money instance
     * @param Money $money
     * @return self
     */
    public function __invoke(
        Money $money  = null,
        $showDecimals = null,
        $locale       = null,
        $pattern      = null
    ) {

        return parent::__invoke(
            $money->getAmount() / $money->getCurrency()->getSubUnit(),
            $money->getCurrency()->getCurrencyCode(),
            $showDecimals,
            $locale,
            $pattern
        );


    }

    /**
     * The 3-letter ISO 4217 currency code indicating the currency to use
     *
     * @param  string|Currency $currencyCode
     * @return MoneyFormat
     */
    public function setCurrencyCode($currencyCode)
    {
        if($currencyCode instanceof Currency) {
            $currencyCode = $currencyCode->getCurrencyCode();
        }
        return parent::setCurrencyCode($currencyCode);
    }

}
