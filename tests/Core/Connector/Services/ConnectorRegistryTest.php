<?php

namespace Smolblog\Core\Connector\Services;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Smolblog\Core\Connector\Connector;
use Smolblog\Framework\Exceptions\RegistrationException;

abstract class TestConnector implements Connector {
	public static function getSlug(): string { return 'test'; }
}

final class ConnectorRegistryTest extends TestCase {
	public function testItRegistersConnectors() {
		$this->assertEquals(Connector::class, ConnectorRegistry::getInterfaceToRegister());
	}

	public function testAConnectorCanBeRegisteredAndRetrieved() {
		$connector = $this->createStub(TestConnector::class);

		$container = $this->createStub(ContainerInterface::class);
		$container->method('get')->willReturn($connector);
		$container->method('has')->willReturn(true);

		$config = [TestConnector::class];

		$reg = new ConnectorRegistry(container: $container, configuration: $config);

		$this->assertTrue($reg->has('test'));
		$this->assertInstanceOf(TestConnector::class, $reg->get('test'));
	}
}
