<?php
declare(strict_types=1);

namespace NetglueMoneyTest\Validator;

use NetglueMoney\Service\CurrencyList;
use NetglueMoney\Validator\CurrencyCode;
use NetglueMoneyTest\Framework\TestCase;

class CurrencyCodeTest extends TestCase
{

    public function testBasic() : void
    {
        $validator = new CurrencyCode(new CurrencyList());

        $this->assertFalse($validator->isValid(123));
        $this->assertArrayHasKey(CurrencyCode::INVALID, $validator->getMessages());

        $this->assertFalse($validator->isValid([]));
        $this->assertArrayHasKey(CurrencyCode::INVALID, $validator->getMessages());

        $this->assertFalse($validator->isValid('ZZZZZ'));
        $this->assertArrayHasKey(CurrencyCode::NOT_MATCH, $validator->getMessages());

        $this->assertFalse($validator->isValid('ZZZ'));
        $this->assertArrayHasKey(CurrencyCode::NOT_FOUND, $validator->getMessages());

        $this->assertTrue($validator->isValid('GBP'));
        $this->assertTrue($validator->isValid('USD'));
    }
}
