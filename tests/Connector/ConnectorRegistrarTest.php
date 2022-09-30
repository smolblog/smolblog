<?php

namespace Smolblog\Core\Connector;

use PHPUnit\Framework\TestCase;

abstract class ConnectorMock implements Connector {
	public const CONFIG = ['slug' => 'camelot'];
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
		$actual = $connectors->retrieve('camelot');

		$this->assertEquals(
			$expected->id,
			$actual->id
		);
	}

	public function testRegistrarGivesNullWhenNotFound() {
		$connectors = new ConnectorRegistrar();
		$this->assertNull($connectors->retrieve('nope'));
	}
}
