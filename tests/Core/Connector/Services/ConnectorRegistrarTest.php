<?php

namespace Smolblog\Core\Connector\Services;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Smolblog\Core\Connector\Connector;
use Smolblog\Framework\Exceptions\RegistrationException;

final class ConnectorRegistrarTest extends TestCase {
	public function testAConnectorCanBeRegisteredAndRetrieved() {
		$connector = $this->createStub(Connector::class);
		$connectorClass = get_class($connector);

		$container = $this->createStub(ContainerInterface::class);
		$container->method('get')->willReturn($connector);
		$container->method('has')->willReturn(true);

		$config = ['test' => $connectorClass];

		$reg = new ConnectorRegistrar(container: $container, configuration: $config);

		$this->assertTrue($reg->has('test'));
		$this->assertInstanceOf($connectorClass, $reg->get('test'));
	}

	public function testItThrowsAnExceptionIfItIsNotAConnector() {
		$this->expectException(RegistrationException::class);

		$container = $this->createStub(ContainerInterface::class);
		$config = ['test' => self::class];

		$reg = new ConnectorRegistrar(container: $container, configuration: $config);
	}
}
