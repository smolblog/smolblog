<?php

namespace Smolblog\Core\Connector;

use PHPUnit\Framework\TestCase;

abstract class ConnectorMock implements Connector {
	public static function config(): ConnectorConfig { return new ConnectorConfig(slug: 'camelot'); }
	public readonly string $id;

	public function __construct() {
		$this->id = uniqid();
	}
}

final class ConnectorRegistrarTest extends TestCase {
	public function testConnectorCanBeRegisteredAndRetrieved() {
		$expected = $this->getMockForAbstractClass(ConnectorMock::class);

		$connectors = new ConnectorRegistrar();
		$connectors->register(class: ConnectorMock::class, factory: fn() => $expected);
		$actual = $connectors->get('camelot');

		$this->assertEquals(
			$expected->id,
			$actual->id
		);
	}
}
