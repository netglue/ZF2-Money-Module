<?php
declare(strict_types=1);

namespace NetglueMoneyTest\Factory;

use NetglueMoney\Factory\CurrencyListFactory;
use NetglueMoney\Service\CurrencyList;
use NetglueMoneyTest\Framework\TestCase;
use Psr\Container\ContainerInterface;

class CurrencyListFactoryTest extends TestCase
{

    public function testCanBeRegisteredAsFactory() : void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->willReturn([
            'ng_money' => [
                'allowCurrencies' => [
                    'GBP', 'USD',
                ],
                'excludeCurrencies' => [
                    'USD',
                ],
            ],
        ]);
        $factory = new CurrencyListFactory();
        /** @var CurrencyList $list */
        $list = $factory($container->reveal());
        $this->assertInstanceOf(CurrencyList::class, $list);

        $this->assertTrue($list->isAllowed('GBP'));
        $this->assertFalse($list->isAllowed('USD'));
        $this->assertFalse($list->isAllowed('ZAR'));
    }
}
