<?php

namespace Smolblog\Foundation\Value\Traits;

use PHPUnit\Framework\Attributes\CoversTrait;
use Smolblog\Foundation\Value;
use Smolblog\Test\TestCase;

readonly class ExampleServiceConfiguration extends Value implements ServiceConfiguration {
	use ServiceConfigurationKit;
	public function __construct(string $key, public string $name) {
		$this->key = $key;
	}
}

#[CoversTrait(ServiceConfigurationKit::class)]
final class ServiceConfigurationTest extends TestCase {
	public function testItWillCorrectlyRetrieveTheKey() {
		$config = new ExampleServiceConfiguration('key', 'test');

		$this->assertEquals('key', $config->key);
		$this->assertEquals('key', $config->getKey());
	}
}
