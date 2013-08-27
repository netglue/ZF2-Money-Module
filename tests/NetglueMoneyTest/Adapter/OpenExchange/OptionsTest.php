<?php

namespace NetglueMoneyTest\Adapter\OpenExchange;

use NetglueMoneyTest\Framework\TestCase;

use NetglueMoney\Adapter\OpenExchange\Options;

use DateTime;

class OptionsTest extends TestCase {
	
	public function testSetGetAppId() {
		$o = new Options;
		$this->assertSame($o, $o->setAppId('foo'));
		$this->assertSame('foo', $o->getAppId());
	}
	
}