<?php
declare(strict_types=1);

namespace NetglueMoneyTest\Factory;

use NetglueMoney\Factory\CurrencySelectFactory;
use NetglueMoney\Form\Element\SelectCurrency;
use NetglueMoney\Service\CurrencyList;
use Psr\Container\ContainerInterface;
use NetglueMoneyTest\Framework\TestCase;

class CurrencySelectFactoryTest extends TestCase
{

    public function testFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $list = new CurrencyList();
        $container->get(CurrencyList::class)->willReturn($list);
        $factory = new CurrencySelectFactory();
        $element = ($factory)($container->reveal());
        $this->assertInstanceOf(SelectCurrency::class, $element);
    }
}
