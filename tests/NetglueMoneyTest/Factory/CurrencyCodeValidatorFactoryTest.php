<?php
declare(strict_types=1);

namespace NetglueMoneyTest\Factory;

use NetglueMoney\Factory\CurrencyCodeValidatorFactory;
use NetglueMoney\Service\CurrencyList;
use NetglueMoney\Validator\CurrencyCode;
use Psr\Container\ContainerInterface;
use NetglueMoneyTest\Framework\TestCase;

class CurrencyCodeValidatorFactoryTest extends TestCase
{

    public function testFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $list = new CurrencyList();
        $container->get(CurrencyList::class)->willReturn($list);
        $factory = new CurrencyCodeValidatorFactory();
        /** @var CurrencyCode $validator */
        $validator = ($factory)($container->reveal());
        $this->assertInstanceOf(CurrencyCode::class, $validator);
    }
}
