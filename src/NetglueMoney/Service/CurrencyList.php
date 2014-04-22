<?php

namespace NetglueMoney\Service;

use NetglueMoney\Money\Currency;
use NetglueMoney\Exception;

class CurrencyList
{

    /**
     * An array of allowed currencies.
     * By default all currencies are allowed
     * @var array
     */
    protected $allow;

    /**
     * Default Currencies
     * @var array
     */
    protected $defaults;

    /**
     * Constructor populates allowed codes with defaults and optionally filters those passed as an argument
     * @return void
     */
    public function __construct($allow = NULL)
    {
        $this->defaults = Currency::getAvailableCurrencies();
        if(NULL !== $allow) {
            $this->setAllow($allow);
        }
    }

    /**
     * Set the allowed currencies to those provided
     * @param array $allow
     * @return self
     */
    public function setAllow(array $allow)
    {
        $this->allow = NULL;
        foreach($allow as $code) {
            $this->add($code);
        }
        return $this;
    }

    /**
     * Return the list of allowed codes
     * @return array
     */
    public function getAllow()
    {
        if(NULL !== $this->allow) {
            return $this->allow;
        }
        return $this->defaults;
    }

    /**
     * Whether the given code is allowed
     * @param string $code
     * @return bool
     */
    public function isAllowed($code)
    {
        return in_array($code, $this->getAllow(), true);
    }

    /**
     * Add a code to the allowed list
     * @param string $code
     * @return self
     */
    public function add($code)
    {
        if(!is_string($code)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a string as it\'s single argument. Recieved %s',
                __FUNCTION__,
                gettype($code)
            ));
        }
        $code = trim(strtoupper($code));
        if(!in_array($code, $this->defaults, true)) {
            throw new Exception\InvalidCurrencyCodeException(sprintf(
                '%s is not a valid ISO 4217 Currency code',
                $code
            ));
        }
        if(!is_array($this->allow)) {
            $this->allow = array();
        }
        $this->allow[] = $code;
        return $this;
    }

    /**
     * Remove a currency code from the allow list
     * @param array|string $code
     * @return self
     */
    public function remove($code)
    {
        if(is_array($code)) {
            foreach($code as $c) {
                $this->remove($c);
            }
            return $this;
        }
        if(!is_string($code)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a string as it\'s single argument. Recieved %s',
                __FUNCTION__,
                gettype($code)
            ));
        }
        if(!is_array($this->allow)) {
            $this->allow = $this->defaults;
        }
        $code = trim(strtoupper($code));
        if($this->isAllowed($code)) {
            if($key = array_search($code, $this->allow)) {
                unset($this->allow[$key]);
            }
        }
        return $this;
    }

}
