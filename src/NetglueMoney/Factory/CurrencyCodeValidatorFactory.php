<?php
declare(strict_types=1);

namespace NetglueMoney\Factory;

use NetglueMoney\Service\CurrencyList;
use NetglueMoney\Validator\CurrencyCode;
use Psr\Container\ContainerInterface;

class CurrencyCodeValidatorFactory
{
    public function __invoke(ContainerInterface $container, $name = null, $options = null)
    {
        $list = $container->get(CurrencyList::class);
        return new CurrencyCode($list, $options);
    }
}
