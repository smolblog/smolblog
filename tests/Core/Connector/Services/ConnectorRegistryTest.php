<?php

namespace Smolblog\Core\Connector\Services;

use Smolblog\Test\TestCase;
use Psr\Container\ContainerInterface;
use Smolblog\Core\Connector\Connector;
use Smolblog\Core\Connector\ConnectorConfiguration;

abstract class MinConnector implements Connector {
	public static function getConfiguration(): ConnectorConfiguration {
		return new ConnectorConfiguration(key: 'test');
	}
}

abstract class MaxConnector implements Connector {
	public static function getConfiguration(): ConnectorConfiguration {
		return new ConnectorConfiguration(key: 'boom', pushEnabled: true, pullEnabled: true);
	}
}

final class ConnectorRegistryTest extends TestCase {
	public function testItRegistersConnectors() {
		$this->assertEquals(Connector::class, ConnectorRegistry::getInterfaceToRegister());
	}

	public function testAMinimalConnectorCanBeRegisteredAndRetrieved() {
		$connector = $this->createStub(MinConnector::class);

		$container = $this->createStub(ContainerInterface::class);
		$container->method('get')->willReturn($connector);
		$container->method('has')->willReturn(true);

		$config = [MinConnector::class];

		$reg = new ConnectorRegistry(container: $container, configuration: $config);

		$this->assertTrue($reg->has('test'));
		$this->assertInstanceOf(MinConnector::class, $reg->get('test'));
		$this->assertEmpty($reg->pushConnectors);
		$this->assertEmpty($reg->pullConnectors);
	}

	public function testAFullConnectorCanBeRegisteredAndRetrieved() {
		$connector = $this->createStub(MaxConnector::class);

		$container = $this->createStub(ContainerInterface::class);
		$container->method('get')->willReturn($connector);
		$container->method('has')->willReturn(true);

		$config = [MaxConnector::class];

		$reg = new ConnectorRegistry(container: $container, configuration: $config);

		$this->assertTrue($reg->has('boom'));
		$this->assertInstanceOf(MaxConnector::class, $reg->get('boom'));
		$this->assertEquals(['boom'], $reg->pushConnectors);
		$this->assertEquals(['boom'], $reg->pullConnectors);
	}
}
