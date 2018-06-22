<?php
declare(strict_types=1);

namespace NetglueMoney\Factory;

use NetglueMoney\Form\Element\SelectCurrency;
use NetglueMoney\Service\CurrencyList;
use Psr\Container\ContainerInterface;

class CurrencySelectFactory
{
    public function __invoke(ContainerInterface $container, $name = null, $options = null) : SelectCurrency
    {
        return new SelectCurrency(
            $container->get(CurrencyList::class),
            null,
            $options
        );
    }
}
