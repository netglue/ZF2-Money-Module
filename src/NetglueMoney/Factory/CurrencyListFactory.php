<?php
declare(strict_types=1);

namespace NetglueMoney\Factory;

use NetglueMoney\Service\CurrencyList;
use Psr\Container\ContainerInterface;

class CurrencyListFactory
{
    public function __invoke(ContainerInterface $container) : CurrencyList
    {
        $config = $container->get('config');
        $config = isset($config['ng_money']) ? $config['ng_money'] : [];
        $list = new CurrencyList;

        if (isset($config['allowCurrencies']) && is_array($config['allowCurrencies'])) {
            $list->setAllow($config['allowCurrencies']);
        }
        if (isset($config['excludeCurrencies'])  && is_array($config['excludeCurrencies'])) {
            $list->remove($config['excludeCurrencies']);
        }

        return $list;
    }
}
