<?php

namespace Smolblog\Core\Federation;

use Smolblog\Test\TestCase;
use Psr\Container\ContainerInterface;

abstract class TestFollowerProvider implements FollowerProvider {
	public static function getSlug(): string { return 'test'; }
}

final class FollowerProviderRegistryTest extends TestCase {
	public function testItRegistersFollowerProviders() {
		$this->assertEquals(FollowerProvider::class, FollowerProviderRegistry::getInterfaceToRegister());
	}

	public function testAFollowerProviderCanBeRegisteredAndRetrieved() {
		$followerProvider = $this->createStub(TestFollowerProvider::class);

		$container = $this->createStub(ContainerInterface::class);
		$container->method('get')->willReturn($followerProvider);
		$container->method('has')->willReturn(true);

		$config = [TestFollowerProvider::class];

		$reg = new FollowerProviderRegistry(container: $container, configuration: $config);

		$this->assertTrue($reg->has('test'));
		$this->assertInstanceOf(TestFollowerProvider::class, $reg->get('test'));
	}
}
