<?php

namespace Smolblog\Core\Registrars;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Connector;

/** @backupStaticAttributes enabled */
final class ConnectorRegistrarTest extends TestCase {
	public function testConnectorCanBeRegisteredAndRetrieved() {
		$expected = $this->getMockForAbstractClass(Connector::class);
		$expected->expects($this->any())
             ->method('slug')
             ->will($this->returnValue('camelot'));

		ConnectorRegistrar::register(connector: $expected);
		$actual = ConnectorRegistrar::retrieve('camelot');

		$this->assertEquals(
			$expected->slug(),
			$actual->slug()
		);
	}

	public function testRegistrarGivesNullWhenNotFound() {
		$this->assertNull(ConnectorRegistrar::retrieve('nope'));
	}
}
