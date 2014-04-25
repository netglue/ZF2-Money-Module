<?php

namespace NetglueMoney\Service;

interface CurrencyListAwareInterface
{
    /**
     * Set Currency list to check allowed currencies against
     * @param CurrencyList $list
     * @return self
     */
    public function setCurrencyList(CurrencyList $list);

}
