<?php

namespace NetglueMoneyTest\Adapter\OpenExchange;

use NetglueMoneyTest\Framework\TestCase;

use NetglueMoney\Adapter\OpenExchange\Options;
use NetglueMoney\Adapter\OpenExchange\Adapter;

use DateTime;

class AdapterTest extends TestCase {
	
	public function testGetSupportedCurrenciesWithoutCache() {
		$o = new Options;
		$a = new Adapter($o);
		$value = $a->getSupportedCurrencyCodes();
		$this->assertInternalType('array', $value);
		$this->assertContainsOnly('string', $value);
		foreach($value as $code) {
			$this->assertTrue( (bool) preg_match('/^[A-Z]{3}$/', $code) );
		}
	}
	
	public function testGetSupportedCurrenciesIsCached() {
		$cache = \Zend\Cache\StorageFactory::factory(array(
			'adapter' => array(
				'name' => 'filesystem'
			)
		));
		$o = new Options;
		$a = new Adapter($o);
		$a->setCache($cache);
		$value = $a->getSupportedCurrencyCodes();
		$this->assertInternalType('array', $value);
		$this->assertTrue($cache->hasItem('openExchangeSupportedCurrencies'));
		$data = unserialize($cache->getItem('openExchangeSupportedCurrencies'));
		$this->assertSame($value, $data);
	}
	
	/**
	 * @expectedException \NetglueMoney\Adapter\Exception\ExceptionInterface
	 */
	public function testGetLatestRatesWithEmptyApiIdThrowsException() {
		$o = new Options;
		$a = new Adapter($o);
		$value = $a->getCurrentRates();
	}
	
	/**
	 * @expectedException \NetglueMoney\Adapter\Exception\ExceptionInterface
	 */
	public function testGetLatestRatesWithInvalidApiIdThrowsException() {
		$o = new Options;
		$o->setAppId('foo');
		$a = new Adapter($o);
		$value = $a->getCurrentRates();
	}
	
	public function testGetLatestRates() {
		$a = $this->getValidAdapter();
		$o = $a->getOptions();
		$value = $a->getCurrentRates();
		// Make sure the returned object is roughly what we expect based on the docs.
		$this->assertInstanceOf('\StdClass', $value);
		$this->assertTrue(isset($value->timestamp));
		$this->assertSame($o->getDefaultBaseCurrency(), $value->base);
		$this->assertInstanceOf('\StdClass', $value->rates);
	}
	
	public function testGetLatestRatesIsCached() {
		$cache = \Zend\Cache\StorageFactory::factory(array(
			'adapter' => array(
				'name' => 'filesystem'
			)
		));
		$a = $this->getValidAdapter();
		$a->setCache($cache);
		$o = $a->getOptions();
		$value = $a->getCurrentRates();
		$key = 'openExchangeLatest'.$o->getBaseCurrency();
		$this->assertTrue($cache->hasItem($key));
		$data = unserialize($cache->getItem($key));
		// Objects are not the same instance due to a trip through serialisation
		$this->assertSame(json_encode($value), json_encode($data));
	}
	
	public function getValidAdapter() {
		$o = new Options;
		$o->setAppId(OE_VALID_API_ID);
		$a = new Adapter;
		$a->setOptions($o);
		return $a;
	}
	
	public function getValidAdapterWithCache() {
		$a = $this->getValidAdapter();
		$cache = \Zend\Cache\StorageFactory::factory(array(
			'adapter' => array(
				'name' => 'filesystem'
			)
		));
		$a->setCache($cache);
		return $a;
	}
	
	/**
	 * @expectedException \NetglueMoney\Adapter\Exception\ExceptionInterface
	 */
	public function testGetRateThrowsExceptionForInvalidCode() {
		$a = $this->getValidAdapterWithCache();
		$a->getRate('ZZZ', 'ABC');
	}
	
}