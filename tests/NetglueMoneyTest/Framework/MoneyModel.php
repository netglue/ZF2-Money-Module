<?php
declare(strict_types=1);

namespace NetglueMoneyTest\Framework;

use NetglueMoney\Money\Currency;
use NetglueMoney\Money\Money;

class MoneyModel
{
    /** @var Money|null */
    public $money;

    /** @var Money|null */
    public $optionalMoney;

    public function setMoney(Money $money) : void
    {
        $this->money = $money;
    }

    public function getMoney() : Money
    {
        if (! $this->money) {
            $this->money = new Money(0, new Currency('USD'));
        }
        return $this->money;
    }

    public function getArrayCopy() : array
    {
        return [
            'money' => $this->money,
            'optionalMoney' => $this->optionalMoney,
        ];
    }

    public function setOptionalMoney(?Money $money) : void
    {
        $this->optionalMoney = $money;
    }

    public function getOptionalMoney() :? Money
    {
        return $this->optionalMoney;
    }
}
