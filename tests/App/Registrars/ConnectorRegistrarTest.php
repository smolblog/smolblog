<?php

namespace Smolblog\App\Registrars;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Smolblog\Core\Connector\Connector;

abstract class ConnectorMock implements Connector {
	public readonly string $id;

	public function __construct() {
		$this->id = uniqid();
	}
}

final class ConnectorRegistrarTest extends TestCase {
	public function testConnectorCanBeRegisteredAndRetrieved() {
		$expected = $this->getMockForAbstractClass(ConnectorMock::class);

		$container = $this->createStub(ContainerInterface::class);
		$container->method('has')->willReturn(true);
		$container->method('get')->willReturn($expected);

		$connectors = new ConnectorRegistrar(container: $container);
		$connectors->register(key: 'camelot', class: ConnectorMock::class);
		$actual = $connectors->get('camelot');

		$this->assertEquals(
			$expected->id,
			$actual->id
		);
	}
}
