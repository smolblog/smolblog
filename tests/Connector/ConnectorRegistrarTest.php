<?php

namespace Smolblog\Core\Connector;

use PHPUnit\Framework\TestCase;

final class ConnectorRegistrarTest extends TestCase {
	public function testConnectorCanBeRegisteredAndRetrieved() {
		$expected = $this->getMockForAbstractClass(Connector::class);
		$expected->expects($this->any())
             ->method('slug')
             ->will($this->returnValue('camelot'));

		$connectors = new ConnectorRegistrar();
		$connectors->register(connector: $expected);
		$actual = $connectors->retrieve('camelot');

		$this->assertEquals(
			$expected->slug(),
			$actual->slug()
		);
	}

	public function testRegistrarGivesNullWhenNotFound() {
		$connectors = new ConnectorRegistrar();
		$this->assertNull($connectors->retrieve('nope'));
	}
}
